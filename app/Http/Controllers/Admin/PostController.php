<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Support\AdminTableSort;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $query = AdminTableSort::apply(
            Post::with('author')->withRichText(['summary_en', 'summary_bn']),
            $allowedSorts,
            $sortBy,
            $sortDirection
        );

        if (! $user->can('posts.edit.any')) {
            $query->where('user_id', $user->id);
        }

        $posts = $query->paginate(20)->withQueryString();
        
        return view('admin.posts.index', compact('posts', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $this->authorize('create', Post::class);

        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Post::class);
        $locale = app()->getLocale();
        $otherLocale = $locale === 'en' ? 'bn' : 'en';
        
        $validated = $request->validate([
            "title_{$locale}" => 'required|max:500',
            "title_{$otherLocale}" => 'nullable|max:500',
            "slug_{$locale}" => "nullable|max:500|unique:posts,slug_{$locale}",
            "slug_{$otherLocale}" => "nullable|max:500|unique:posts,slug_{$otherLocale}",
            "body_{$locale}" => 'required|string',
            "body_{$otherLocale}" => 'nullable|string',
            "summary_{$locale}" => 'nullable|string|max:5000',
            "summary_{$otherLocale}" => 'nullable|string|max:5000',
            'status' => 'required|in:draft,pending,published',
            'category_id' => 'nullable|exists:categories,id',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            "meta_title_{$locale}" => 'nullable|max:70',
            "meta_title_{$otherLocale}" => 'nullable|max:70',
            "meta_description_{$locale}" => 'nullable|max:170',
            "meta_description_{$otherLocale}" => 'nullable|max:170',
            'canonical_url' => 'nullable|url|max:500',
            'featured_image' => 'nullable|image|max:5120',
        ]);

        $user = Auth::user();

        if ($validated['status'] === 'published' && ! $user->can('posts.publish')) {
            $validated['status'] = 'pending';
        }
        if ($validated['status'] === 'pending' && ! $user->can('posts.submit_review')) {
            abort(403, 'You are not allowed to submit posts for review.');
        }

        $postData = $this->preparePostData($validated);

        $post = Post::create([
            ...$postData,
            'user_id' => $user->id,
        ]);
        
        $post->categories()->sync(($validated['category_id'] ?? null) ? [$validated['category_id']] : []);

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        $locale = app()->getLocale();
        $otherLocale = $locale === 'en' ? 'bn' : 'en';
        
        $validated = $request->validate([
            "title_{$locale}" => 'required|max:500',
            "title_{$otherLocale}" => 'nullable|max:500',
            "slug_{$locale}" => "nullable|max:500|unique:posts,slug_{$locale}," . $post->id,
            "slug_{$otherLocale}" => "nullable|max:500|unique:posts,slug_{$otherLocale}," . $post->id,
            "body_{$locale}" => 'required|string',
            "body_{$otherLocale}" => 'nullable|string',
            "summary_{$locale}" => 'nullable|string|max:5000',
            "summary_{$otherLocale}" => 'nullable|string|max:5000',
            'status' => 'required|in:draft,pending,published',
            'category_id' => 'nullable|exists:categories,id',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            "meta_title_{$locale}" => 'nullable|max:70',
            "meta_title_{$otherLocale}" => 'nullable|max:70',
            "meta_description_{$locale}" => 'nullable|max:170',
            "meta_description_{$otherLocale}" => 'nullable|max:170',
            'canonical_url' => 'nullable|url|max:500',
        ]);

        if ($validated['status'] === 'published' && ! $request->user()->can('posts.publish')) {
            $validated['status'] = 'pending';
        }
        if ($validated['status'] === 'pending' && ! $request->user()->can('posts.submit_review')) {
            abort(403, 'You are not allowed to submit posts for review.');
        }

        $post->update($this->preparePostData($validated, $post));
        
        $post->categories()->sync(($validated['category_id'] ?? null) ? [$validated['category_id']] : []);
        
        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully!');
    }

    private function preparePostData(array $validated, ?Post $post = null): array
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

        return [
            'title' => $titleBn ?: $titleEn, // default to BN title if exists for base title
            'title_en' => $titleEn,
            'title_bn' => $titleBn,
            'slug' => $this->uniqueSlug($validated['slug_bn'] ?? $validated['slug_en'] ?? null, $titleBn ?: $titleEn, 'slug', $post),
            'slug_en' => $this->uniqueSlug($validated['slug_en'] ?? null, $titleEn, 'slug_en', $post),
            'slug_bn' => $this->uniqueSlug($validated['slug_bn'] ?? null, $titleBn, 'slug_bn', $post),
            'content' => $bodyBn ?: $bodyEn,
            'excerpt' => Str::limit(strip_tags($summaryBn ?: $summaryEn ?: $bodyBn ?: $bodyEn), 500, ''),
            'body_en' => $bodyEnRaw ? $bodyEn : null, // Let rich text handle existing
            'body_bn' => $bodyBnRaw ? $bodyBn : null, // Let rich text handle existing
            'summary_en' => $summaryEnRaw ? $summaryEn : null,
            'summary_bn' => $summaryBnRaw ? $summaryBn : null,
            'status' => $validated['status'],
            'meta_title' => $validated['meta_title'] ?? $validated['meta_title_bn'] ?? $validated['meta_title_en'] ?? null,
            'meta_description' => $validated['meta_description'] ?? $validated['meta_description_bn'] ?? $validated['meta_description_en'] ?? null,
            'meta_title_en' => $validated['meta_title_en'] ?? $post?->meta_title_en,
            'meta_title_bn' => $validated['meta_title_bn'] ?? $post?->meta_title_bn,
            'meta_description_en' => $validated['meta_description_en'] ?? $post?->meta_description_en,
            'meta_description_bn' => $validated['meta_description_bn'] ?? $post?->meta_description_bn,
            'canonical_url' => $validated['canonical_url'] ?? $post?->canonical_url,
        ];
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
}
