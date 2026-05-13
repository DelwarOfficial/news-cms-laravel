<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\District;
use App\Models\Division;
use App\Models\Language;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Upazila;
use App\Models\User;
use App\Support\AdminTableSort;
use App\Support\FrontendCache;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct(private readonly RichTextSanitizer $richTextSanitizer)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorize('viewAny', Post::class);
        
        $allowedSorts = ['title', 'status', 'created_at', 'updated_at'];
        [$sortBy, $sortDirection] = AdminTableSort::resolve($request, $allowedSorts);
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', 'all');
        $allowedStatuses = ['all', 'draft', 'pending', 'published', 'scheduled', 'archived'];
        $status = in_array($status, $allowedStatuses, true) ? $status : 'all';
        $perPage = (int) $request->query('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $baseQuery = Post::query();

        if (! $user->can('posts.edit.any')) {
            $baseQuery->where('user_id', $user->id);
        }

        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $query = (clone $baseQuery)
            ->with(['author:id,name', 'bylineAuthor:id,name', 'primaryCategory:id,name', 'categories:id,name'])
            ->withRichText(['summary_en', 'summary_bn'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('title_en', 'like', "%{$search}%")
                        ->orWhere('title_bn', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('slug_en', 'like', "%{$search}%")
                        ->orWhere('slug_bn', 'like', "%{$search}%");
                });
            })
            ->when($status !== 'all', fn ($query) => $query->where('status', $status));

        $posts = AdminTableSort::apply($query, $allowedSorts, $sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();
        
        return view('admin.posts.index', compact('posts', 'sortBy', 'sortDirection', 'search', 'status', 'statusCounts', 'perPage'));
    }

    public function create()
    {
        $this->authorize('create', Post::class);

        $categories = Category::with('parent')->orderBy('parent_id')->orderBy('order')->orderBy('name')->get();
        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);
        $authors = User::query()->orderBy('name')->get(['id', 'name']);
        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::active()->with('division')->orderBy('name')->get();
        $upazilas = Upazila::active()->with('district')->orderBy('name')->get();

        return view('admin.posts.create', compact('categories', 'tags', 'authors', 'divisions', 'districts', 'upazilas'));
    }

    public function store(StorePostRequest $request)
    {
        $this->authorize('create', Post::class);
        $validated = $request->validated();

        $user = Auth::user();

        if ($validated['status'] === 'published' && ! $user->can('posts.publish')) {
            $validated['status'] = 'pending';
        }
        if ($validated['status'] === 'pending' && ! $user->can('posts.submit_review')) {
            abort(403, 'You are not allowed to submit posts for review.');
        }
        $this->ensureDistrictBelongsToDivision($validated);
        $this->ensureUpazilaBelongsToDistrict($validated);

        $postData = $this->preparePostData($validated, null, $request);

        $post = Post::create([
            ...$postData,
            'user_id' => $user->id,
        ]);
        
        $post->categories()->sync([$validated['category_id'] => ['is_primary' => true]]);
        $post->tags()->sync($validated['tag_ids'] ?? []);
        FrontendCache::flushContent();

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        
        $categories = Category::with('parent')->orderBy('parent_id')->orderBy('order')->orderBy('name')->get();
        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);
        $authors = User::query()->orderBy('name')->get(['id', 'name']);
        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::active()->with('division')->orderBy('name')->get();
        $upazilas = Upazila::active()->with('district')->orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'categories', 'tags', 'authors', 'divisions', 'districts', 'upazilas'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);
        $post->loadMissing(['categories', 'tags']);
        $validated = $request->validated();

        if ($validated['status'] === 'published' && ! $request->user()->can('posts.publish')) {
            $validated['status'] = 'pending';
        }
        if ($validated['status'] === 'pending' && ! $request->user()->can('posts.submit_review')) {
            abort(403, 'You are not allowed to submit posts for review.');
        }
        $this->ensureDistrictBelongsToDivision($validated);
        $this->ensureUpazilaBelongsToDistrict($validated);

        $post->update($this->preparePostData($validated, $post, $request));
        
        $post->categories()->sync([$validated['category_id'] => ['is_primary' => true]]);
        $post->tags()->sync($validated['tag_ids'] ?? []);
        FrontendCache::flushContent();
        
        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully!');
    }

    private function preparePostData(array $validated, ?Post $post = null, ?Request $request = null): array
    {
        // We might not receive the other locale's fields if they weren't submitted, 
        // so we fall back to existing data if this is an update, or null if creation.
        $titleEn = $validated['title_en'] ?? $post?->title_en;
        $titleBn = $validated['title_bn'] ?? $post?->title_bn;
        
        $bodyEnRaw = $validated['body_en'] ?? null;
        $bodyBnRaw = $validated['body_bn'] ?? null;
        
        $bodyEn = $bodyEnRaw ? $this->richTextSanitizer->sanitize($bodyEnRaw) : $post?->body_en;
        $bodyBn = $bodyBnRaw ? $this->richTextSanitizer->sanitize($bodyBnRaw) : $post?->body_bn;
        
        $summaryEnRaw = $validated['summary_en'] ?? null;
        $summaryBnRaw = $validated['summary_bn'] ?? null;
        
        $summaryEn = $summaryEnRaw ? $this->richTextSanitizer->sanitize($summaryEnRaw) : $post?->summary_en;
        $summaryBn = $summaryBnRaw ? $this->richTextSanitizer->sanitize($summaryBnRaw) : $post?->summary_bn;

        $excerpt = Str::limit(trim(strip_tags($summaryBn ?: $summaryEn ?: $bodyBn ?: $bodyEn)), 500, '');
        $statusDates = $this->statusDates($validated, $post);
        $featuredImage = $request?->file('featured_image')
            ? $this->storeImage($request, 'featured_image', 'featured')
            : $post?->featured_image;
        $ogImage = $request?->file('og_image')
            ? $this->storeImage($request, 'og_image', 'og')
            : ($validated['og_image'] ?? $post?->og_image);

        return [
            'language_id' => $post?->language_id ?? Language::idForLocale(app()->getLocale()),
            'author_id' => $validated['author_id'] ?? $post?->author_id ?? Auth::id(),
            'title' => $titleBn ?: $titleEn, // default to BN title if exists for base title
            'title_en' => $titleEn,
            'title_bn' => $titleBn,
            'shoulder' => $validated['shoulder'] ?? null,
            'slug' => $this->uniqueSlug($validated['slug_bn'] ?? $validated['slug_en'] ?? null, $titleBn ?: $titleEn, 'slug', $post),
            'slug_en' => $this->uniqueSlug($validated['slug_en'] ?? null, $titleEn, 'slug_en', $post),
            'slug_bn' => $this->uniqueSlug($validated['slug_bn'] ?? null, $titleBn, 'slug_bn', $post),
            'content' => $bodyBn ?: $bodyEn,
            'excerpt' => $excerpt,
            'body_en' => $bodyEnRaw ? $bodyEn : null, // Let rich text handle existing
            'body_bn' => $bodyBnRaw ? $bodyBn : null, // Let rich text handle existing
            'summary_en' => $summaryEnRaw ? $summaryEn : null,
            'summary_bn' => $summaryBnRaw ? $summaryBn : null,
            'featured_image' => $featuredImage,
            'featured_image_alt' => $validated['featured_image_alt'] ?? ($titleBn ?: $titleEn),
            'post_format' => $validated['post_format'] ?? 'standard',
            'status' => $validated['status'],
            ...$statusDates,
            'primary_category_id' => $validated['category_id'] ?? null,
            'meta_title' => $validated['meta_title'] ?? $validated['meta_title_bn'] ?? $validated['meta_title_en'] ?? ($titleBn ?: $titleEn),
            'meta_description' => $validated['meta_description'] ?? $validated['meta_description_bn'] ?? $validated['meta_description_en'] ?? Str::limit($excerpt, 160, ''),
            'meta_title_en' => $validated['meta_title_en'] ?? ($titleEn ?: $post?->meta_title_en),
            'meta_title_bn' => $validated['meta_title_bn'] ?? ($titleBn ?: $post?->meta_title_bn),
            'meta_description_en' => $validated['meta_description_en'] ?? ($excerpt ?: $post?->meta_description_en),
            'meta_description_bn' => $validated['meta_description_bn'] ?? ($excerpt ?: $post?->meta_description_bn),
            'canonical_url' => $validated['canonical_url'] ?? $post?->canonical_url,
            'og_image' => $ogImage ?: $featuredImage,
            'division_id' => $validated['division_id'] ?? null,
            'district_id' => $validated['district_id'] ?? null,
            'upazila_id' => $validated['upazila_id'] ?? null,
            'is_breaking' => (bool) ($validated['is_breaking'] ?? false),
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
            'is_trending' => (bool) ($validated['is_trending'] ?? false),
            'is_editors_pick' => (bool) ($validated['is_editors_pick'] ?? false),
            'is_sticky' => (bool) ($validated['is_sticky'] ?? false),
            'is_photocard' => (bool) ($validated['is_photocard'] ?? false),
            'allow_comments' => (bool) ($validated['allow_comments'] ?? false),
            'show_author' => (bool) ($validated['show_author'] ?? false),
            'show_publish_date' => (bool) ($validated['show_publish_date'] ?? false),
        ];
    }

    private function statusDates(array $validated, ?Post $post = null): array
    {
        return match ($validated['status']) {
            'published' => [
                'published_at' => $validated['published_at'] ?? $post?->published_at ?? now(),
                'scheduled_at' => null,
            ],
            'scheduled' => [
                'published_at' => null,
                'scheduled_at' => $validated['scheduled_at'],
            ],
            default => [
                'published_at' => null,
                'scheduled_at' => null,
            ],
        };
    }

    private function storeImage(Request $request, string $field, string $directory): string
    {
        return 'storage/'.$request->file($field)->store("posts/{$directory}", 'public');
    }

    private function uniqueSlug(?string $requestedSlug, ?string $title, string $column, ?Post $post = null): ?string
    {
        $source = $requestedSlug ?: $title;

        if (! $source) {
            return $post?->{$column};
        }

        $slug = Str::of($source)
            ->lower()
            ->replaceMatches('/[^\pL\pN]+/u', '-')
            ->trim('-')
            ->toString();

        if ($slug === '') {
            return $post?->{$column};
        }

        $candidate = $slug;
        $suffix = 2;

        while (
            Post::where($column, $candidate)
                ->when($post, fn ($query) => $query->whereKeyNot($post->getKey()))
                ->exists()
        ) {
            $candidate = $slug.'-'.$suffix++;
        }

        return $candidate;
    }

    private function ensureDistrictBelongsToDivision(array $validated): void
    {
        if (empty($validated['division_id']) || empty($validated['district_id'])) {
            return;
        }

        $belongs = District::query()
            ->whereKey($validated['district_id'])
            ->where('division_id', $validated['division_id'])
            ->exists();

        abort_unless($belongs, 422, 'Selected district does not belong to the selected division.');
    }

    private function ensureUpazilaBelongsToDistrict(array $validated): void
    {
        if (empty($validated['upazila_id'])) {
            return;
        }

        abort_unless(! empty($validated['district_id']), 422, 'Select a district before selecting an upazila.');

        $belongs = Upazila::query()
            ->whereKey($validated['upazila_id'])
            ->where('district_id', $validated['district_id'])
            ->when(! empty($validated['division_id']), fn ($query) => $query->where('division_id', $validated['division_id']))
            ->exists();

        abort_unless($belongs, 422, 'Selected upazila does not belong to the selected district.');
    }
}
