<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\PopularNewsService;
use App\Support\CategoryRepository;
use App\Support\ArticleFeed;
use App\Support\FallbackDataService;
use App\Models\District;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private readonly PopularNewsService $popularNews,
    ) {
    }

    public function showParent(Request $request, string $parentSlug)
    {
        if ($target = CategoryRepository::redirectTarget($parentSlug)) {
            return redirect('/category/' . $target, 301);
        }

        $category = CategoryRepository::findParent($parentSlug);

        abort_unless($category !== null, 404);

        $categorySlugs = collect($category['children'])->pluck('slug')->push($category['slug'])->all();

        $division = null;
        $district = null;
        $upazila = null;
        $divisions = [];

        if ($parentSlug === 'country-news') {
            [$division, $district, $upazila] = $this->normalisedLocationFilters($request);

            try {
                $divisions = District::allDivisions();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to load divisions: " . $e->getMessage());
            }
        }

        return $this->renderCategory(
            $category,
            ArticleFeed::categoryArticles($categorySlugs, FallbackDataService::getArticles(), 30, $division, $district, $upazila),
            [
                ['title' => 'হোম', 'url' => route('home')],
                ['title' => $category['name_bn'], 'url' => CategoryRepository::route($category)],
            ],
            $division,
            $district,
            $upazila,
            $divisions
        );
    }

    public function showChild(Request $request, string $parentSlug, string $childSlug)
    {
        $parent = CategoryRepository::findParent($parentSlug);
        $category = CategoryRepository::findChild($parentSlug, $childSlug);

        abort_unless($parent && $category, 404);

        return $this->renderCategory(
            $category,
            ArticleFeed::categoryArticles([$category['slug']], FallbackDataService::getArticles()),
            [
                ['title' => 'হোম', 'url' => route('home')],
                ['title' => $parent['name_bn'], 'url' => CategoryRepository::route($parent)],
                ['title' => $category['name_bn'], 'url' => CategoryRepository::route($category)],
            ]
        );
    }

    public function sitemap()
    {
        $urls = CategoryRepository::flat()
            ->map(fn(array $category) => [
                'loc' => CategoryRepository::route($category),
                'lastmod' => now()->toDateString(),
            ]);

        return response()
            ->view('pages.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    private function renderCategory(array $category, array $categoryArticles, array $breadcrumbs, ?string $division = null, ?string $district = null, ?string $upazila = null, array $divisions = [])
    {
        $popularNews = $this->popularNews->get();
        $categoryName = $category['name_bn'];

        // Build meta title/description if location filtered
        if ($upazila) {
            $metaTitle = "{$upazila}-এর সর্বশেষ সংবাদ | Dhaka Magazine";
            $metaDescription = "বাংলাদেশের {$upazila} উপজেলার সর্বশেষ খবর পড়ুন Dhaka Magazine-এ।";
        } elseif ($district) {
            $metaTitle = "{$district} জেলার সর্বশেষ সংবাদ | Dhaka Magazine";
            $metaDescription = "বাংলাদেশের {$district} জেলার সর্বশেষ খবর পড়ুন Dhaka Magazine-এ।";
        } elseif ($division) {
            $metaTitle = "{$division} বিভাগের সর্বশেষ সংবাদ | Dhaka Magazine";
            $metaDescription = "বাংলাদেশের {$division} বিভাগের সর্বশেষ খবর পড়ুন Dhaka Magazine-এ।";
        } else {
            $metaTitle = $category['meta_title'];
            $metaDescription = $category['meta_description'];
        }
        
        $canonicalUrl = CategoryRepository::route($category);
        $pageImage = $categoryArticles[0]['image_url'] ?? asset('images/dhaka-magazine-color-logo.svg');

        return view('pages.category', compact(
            'category',
            'categoryName',
            'categoryArticles',
            'popularNews',
            'breadcrumbs',
            'metaTitle',
            'metaDescription',
            'canonicalUrl',
            'pageImage',
            'division',
            'district',
            'upazila',
            'divisions'
        ));
    }


    public function districts(Request $request): \Illuminate\Http\JsonResponse
    {
        $division = trim($request->input('division', ''));

        if (! $division) {
            return response()->json([]);
        }

        $divisions = District::allDivisions();

        if (! array_key_exists($division, $divisions)) {
            return response()->json([
                'message' => 'Invalid division selected.',
            ], 422);
        }

        $data = District::forDivision($division);

        return response()->json($data);
    }

    public function upazilas(Request $request): \Illuminate\Http\JsonResponse
    {
        $division = trim($request->input('division', ''));
        $district  = trim($request->input('district', ''));

        if (! $division || ! $district) {
            return response()->json([]);
        }

        $divisions = District::allDivisions();

        if (! array_key_exists($division, $divisions) || ! District::belongsToDivision($division, $district)) {
            return response()->json([
                'message' => 'Invalid division or district selected.',
            ], 422);
        }

        $upazilas = $this->upazilasFor($division, $district);

        $bnMap = \App\Support\LocationDataProvider::getUpazilaBnMap();

        $upazilas = collect($upazilas)
            ->map(function ($upazila) use ($bnMap) {
                if (is_array($upazila)) {
                    $slug = $upazila['slug'] ?? ($upazila['name'] ?? '');
                    $nameBn = $upazila['name_bn'] ?? $this->upazilaLabelBangla($slug, $bnMap);

                    return [
                        'slug' => $slug,
                        'name_bn' => $nameBn,
                    ];
                }

                return [
                    'slug' => $upazila,
                    'name_bn' => $this->upazilaLabelBangla($upazila, $bnMap),
                ];
            })
            ->filter(fn($item) => ! empty($item['slug']))
            ->values()
            ->all();

        return response()->json($upazilas);
    }

    private function upazilaLabelBangla(string $slug, array $bnMap): string
    {
        return $bnMap[$slug] ?? $slug;
    }

    private function normalisedLocationFilters(Request $request): array
    {
        $division = trim((string) $request->query('division', ''));
        $district = trim((string) $request->query('district', ''));
        $upazila = trim((string) $request->query('upazila', ''));
        $divisions = District::allDivisions();

        if ($division === '' || ! array_key_exists($division, $divisions)) {
            return ['', '', ''];
        }

        if ($district !== '' && ! District::belongsToDivision($division, $district)) {
            $district = '';
            $upazila = '';
        }

        if ($upazila !== '' && ($district === '' || ! in_array($upazila, $this->upazilasFor($division, $district), true))) {
            $upazila = '';
        }

        return [$division, $district, $upazila];
    }

    private function upazilasFor(string $division, string $district): array
    {
        $locationData = \App\Support\LocationDataProvider::getLocationData();

        return $locationData[$division]['districts'][$district]['upazilas'] ?? [];
    }
}
