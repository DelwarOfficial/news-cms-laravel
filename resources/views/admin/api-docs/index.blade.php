@extends('admin.layouts.app')

@section('title', 'API Documentation')
@section('page-title', 'API Documentation')

@push('styles')
<style>
    .doc-section { scroll-margin-top: 6rem; }
    .method-badge { display: inline-flex; align-items: center; padding: 0.125rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; min-width: 4rem; justify-content: center; }
    .scope-badge { display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; }
    .endpoint-table td { padding: 0.75rem 1rem; font-size: 0.875rem; }
    .endpoint-table tr:hover { background: var(--color-gray-50); }
    .dark .endpoint-table tr:hover { background: var(--color-gray-800); }
    .arch-card { background: var(--color-white); border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--color-gray-100); overflow: hidden; }
    .dark .arch-card { background: var(--color-gray-900); border-color: var(--color-gray-700); }
    .arch-arrow { color: var(--color-gray-400); font-size: 1.5rem; }
</style>
@endpush

@section('content')

{{-- Quick Nav --}}
<div class="sticky top-0 z-10 -mx-8 px-8 py-3 bg-white/95 backdrop-blur border-b border-gray-100 mb-8 flex flex-wrap gap-2 text-sm">
    <span class="text-gray-400 font-semibold mr-2">Jump to:</span>
    <a href="#quickstart" class="text-blue-600 hover:text-blue-800 font-medium">Quick Start</a>
    <span class="text-gray-300 mx-1">·</span>
    <a href="#api-keys" class="text-blue-600 hover:text-blue-800 font-medium">API Keys</a>
    <span class="text-gray-300 mx-1">·</span>
    <a href="#mobile-api" class="text-blue-600 hover:text-blue-800 font-medium">Public API</a>
    <span class="text-gray-300 mx-1">·</span>
    <a href="#cms-api" class="text-blue-600 hover:text-blue-800 font-medium">CMS API</a>
    <span class="text-gray-300 mx-1">·</span>
    <a href="#endpoints" class="text-blue-600 hover:text-blue-800 font-medium">All Routes</a>
</div>

{{-- Quick Start --}}
<section id="quickstart" class="doc-section mb-10">
    <div class="arch-card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
            <i class="fas fa-rocket text-blue-600"></i> Quick Start
        </h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold shrink-0">1</div>
                    <div><h3 class="font-semibold text-gray-900 text-sm">Create an API Key</h3><p class="text-sm text-gray-500 mt-1">Go to <strong>API Keys</strong> → <strong>Create API Key</strong>. Name it, pick scopes, set limit.</p></div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold shrink-0">2</div>
                    <div><h3 class="font-semibold text-gray-900 text-sm">Copy and Store the Key</h3><p class="text-sm text-gray-500 mt-1">Shown <strong class="text-red-600">only once</strong>. Starts with <code class="bg-gray-200 px-1.5 rounded text-xs">nh_</code>. Keep it safe.</p></div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold shrink-0">3</div>
                    <div><h3 class="font-semibold text-gray-900 text-sm">Test Your Key</h3>
<pre class="bg-gray-900 text-gray-100 rounded-xl p-3 text-xs mt-2 overflow-x-auto"><code># Public (no key needed)
curl https://yourdomain.com/api/v1/posts

# Authenticated
curl -H "X-API-Key: nh_your_key" \
  https://yourdomain.com/api/v1/dashboard</code></pre>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold shrink-0">4</div>
                    <div><h3 class="font-semibold text-gray-900 text-sm">Build Your App</h3><p class="text-sm text-gray-500 mt-1">Use <strong>Public API</strong> (no auth) for frontend/mobile apps. Use <strong>CMS API</strong> (cms scope) for external content push.</p></div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shrink-0">💡</div>
                    <div><h3 class="font-semibold text-gray-900 text-sm">Tip</h3><p class="text-sm text-gray-500 mt-1">Create separate keys for each integration. If compromised, revoke just that key. All responses use <code class="bg-gray-200 px-1.5 rounded text-xs">{data, meta}</code> format.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- API Keys --}}
<section id="api-keys" class="doc-section mb-10">
    <div class="arch-card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
            <i class="fas fa-key text-amber-600"></i> API Keys
        </h2>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="p-5 rounded-xl bg-gray-50 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-3">How to Send</h3>
                <p class="text-sm text-gray-600 mb-3">Pass the key via header:</p>
                <pre class="bg-gray-900 text-gray-100 rounded-xl p-3 text-xs overflow-x-auto"><code>curl -H "X-API-Key: nh_abc123..." \
  https://yourdomain.com/api/v1/posts/manage</code></pre>
                <p class="text-sm text-gray-600 mt-3">Or as <code class="bg-gray-200 px-1 rounded text-xs">Authorization: Bearer nh_xxx</code> or <code class="bg-gray-200 px-1 rounded text-xs">?api_key=nh_xxx</code>.</p>
            </div>
            <div class="p-5 rounded-xl bg-gray-50 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-3">Available Scopes</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div><span class="scope-badge bg-green-100 text-green-700">read</span> <span class="text-xs text-gray-500 ml-1">View content</span></div>
                    <div><span class="scope-badge bg-blue-100 text-blue-700">write</span> <span class="text-xs text-gray-500 ml-1">Create/edit content</span></div>
                    <div><span class="scope-badge bg-purple-100 text-purple-700">media</span> <span class="text-xs text-gray-500 ml-1">Upload media</span></div>
                    <div><span class="scope-badge bg-sky-100 text-sky-700">comments</span> <span class="text-xs text-gray-500 ml-1">Manage comments</span></div>
                    <div><span class="scope-badge bg-amber-100 text-amber-700">cms</span> <span class="text-xs text-gray-500 ml-1">External push</span></div>
                    <div><span class="scope-badge bg-red-100 text-red-700">admin</span> <span class="text-xs text-gray-500 ml-1">Full access</span></div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div class="p-4 rounded-xl border border-gray-200">
                <h4 class="font-semibold text-gray-900 mb-2">How Keys Work</h4>
                <ul class="space-y-1.5 text-xs">
                    <li>• Keys start with <code class="bg-gray-100 px-1 rounded">nh_</code> prefix</li>
                    <li>• Stored as SHA-256 hash (raw key never kept)</li>
                    <li>• Shown only once after creation</li>
                    <li>• Can expire or be toggled off</li>
                    <li>• Rate limited: 60 req/min by default</li>
                </ul>
            </div>
            <div class="p-4 rounded-xl border border-gray-200">
                <h4 class="font-semibold text-gray-900 mb-2">Validation Steps</h4>
                <ol class="space-y-1.5 text-xs list-decimal list-inside">
                    <li>Extract key from request</li>
                    <li>Check <code class="bg-gray-100 px-1 rounded">nh_</code> prefix</li>
                    <li>Hash with SHA-256</li>
                    <li>Look up in database</li>
                    <li>Verify active &amp; not expired</li>
                    <li>Check required scope</li>
                </ol>
            </div>
            <div class="p-4 rounded-xl border border-gray-200">
                <h4 class="font-semibold text-gray-900 mb-2">Status Codes</h4>
                <div class="space-y-1.5 text-xs">
                    <div><span class="font-semibold">200</span> — Success</div>
                    <div><span class="font-semibold">401</span> — Missing/invalid key</div>
                    <div><span class="font-semibold">403</span> — Wrong scope</div>
                    <div><span class="font-semibold">404</span> — Not found</div>
                    <div><span class="font-semibold">422</span> — Validation error</div>
                    <div><span class="font-semibold">429</span> — Rate limit hit</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Public API --}}
<section id="mobile-api" class="doc-section mb-10">
    <div class="arch-card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-3">
            <i class="fas fa-globe text-blue-600"></i> Public API — Read-Only
        </h2>
        <p class="text-sm text-gray-500 mb-6">No auth needed. Returns published content only. 60 req/min.</p>

        @php $mobileEndpoints = [
            ['GET', '/api/v1/posts', 'List posts with filters', 'limit, sort, category_slug, tag_slug, author_id, date_from, date_to, search, is_breaking, is_featured, is_trending, is_editors_pick, is_sticky, post_format'],
            ['GET', '/api/v1/posts/{slug}', 'Single post by slug', 'Increments view_count'],
            ['GET', '/api/v1/posts/breaking', 'Latest 10 breaking', '—'],
            ['GET', '/api/v1/posts/trending', 'Latest 10 trending', '—'],
            ['GET', '/api/v1/posts/popular', 'Top 10 by views', '—'],
            ['GET', '/api/v1/posts/featured', 'Latest 5 featured', '—'],
            ['GET', '/api/v1/posts/editors-pick', "Latest 5 editor's pick", '—'],
            ['POST', '/api/v1/posts/{id}/view', 'Increment view count', '1 req/60s limit'],
            ['GET', '/api/v1/categories', 'Category tree', '—'],
            ['GET', '/api/v1/categories/{slug}/posts', 'Posts in category', 'limit, date_from, date_to, is_breaking, is_featured'],
            ['GET', '/api/v1/search', 'Full-text search', 'q, category_slug, is_breaking, is_featured, is_trending, date_from, date_to'],
        ]; @endphp

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200"><tr>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600 w-20">Method</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Endpoint</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Description</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Notes</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($mobileEndpoints as $ep)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td><span class="method-badge bg-green-100 text-green-700">{{ $ep[0] }}</span></td>
                        <td class="font-mono text-xs">{{ $ep[1] }}</td>
                        <td class="text-sm">{{ $ep[2] }}</td>
                        <td class="text-xs text-gray-500">{{ $ep[3] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Example Response --}}
        <div class="grid md:grid-cols-2 gap-6 mt-6">
            <div class="arch-card p-6 border">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Success</h3>
                <pre class="bg-gray-900 text-gray-100 rounded-xl p-3 text-xs overflow-x-auto"><code>{
  "data": [{
    "id": 1,
    "title": "Article Title",
    "slug": "article-title",
    "excerpt": "Summary...",
    "image_url": "https://...",
    "published_at": "2026-05-14T12:00:00Z",
    "view_count": 42,
    "category": "Sports",
    "category_slug": "sports",
    "author": "Author Name",
    "tags": ["tag1"],
    "locale": "en"
  }],
  "meta": { "page": 1, "limit": 15, "total": 145, "totalPages": 10 }
}</code></pre>
            </div>
            <div class="arch-card p-6 border">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-500"></i> Error</h3>
                <pre class="bg-gray-900 text-gray-100 rounded-xl p-3 text-xs overflow-x-auto"><code>{
  "error": "Validation Error",
  "message": "The given data was invalid.",
  "details": {
    "title": ["The title field is required."]
  }
}</code></pre>
            </div>
        </div>
    </div>
</section>

{{-- CMS API --}}
<section id="cms-api" class="doc-section mb-10">
    <div class="arch-card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-3">
            <i class="fas fa-cloud-upload-alt text-amber-600"></i> CMS API — External Push
        </h2>
        <p class="text-sm text-gray-500 mb-6">Requires <span class="scope-badge bg-amber-100 text-amber-700">cms</span> scope. For external systems to push content. 60 req/min.</p>

        @php $cmsEndpoints = [
            ['GET', '/api/v1/cms/status', 'Health check', '—'],
            ['POST', '/api/v1/cms/posts', 'Create post', 'title, body, status, category_slug, tag_names[], featured_image_url, source_url, source_name, is_breaking, meta_title, meta_description'],
            ['PUT', '/api/v1/cms/posts/{id}', 'Update post', 'Partial update any field'],
            ['DELETE', '/api/v1/cms/posts/{id}', 'Delete (soft)', '—'],
            ['GET', '/api/v1/cms/categories', 'List categories', '—'],
            ['POST', '/api/v1/cms/categories', 'Create category', 'name, slug, parent_slug'],
            ['GET', '/api/v1/cms/tags', 'List tags', '—'],
            ['POST', '/api/v1/cms/tags', 'Create tag', 'name, slug'],
            ['POST', '/api/v1/cms/media', 'Upload file', 'file (multipart)'],
        ]; @endphp

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200"><tr>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600 w-20">Method</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Endpoint</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Description</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Params</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($cmsEndpoints as $ep)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td><span class="method-badge bg-{{ $ep[0] === 'GET' ? 'green' : ($ep[0] === 'POST' ? 'blue' : ($ep[0] === 'PUT' ? 'yellow' : 'red')) }}-100 text-{{ $ep[0] === 'GET' ? 'green' : ($ep[0] === 'POST' ? 'blue' : ($ep[0] === 'PUT' ? 'yellow' : 'red')) }}-700">{{ $ep[0] }}</span></td>
                        <td class="font-mono text-xs">{{ $ep[1] }}</td>
                        <td class="text-sm">{{ $ep[2] }}</td>
                        <td class="text-xs text-gray-500">{{ $ep[3] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 p-5 rounded-xl bg-amber-50 border border-amber-200">
            <h3 class="font-semibold text-amber-900 text-sm mb-4">Smart Post Creation</h3>
            <div class="grid md:grid-cols-3 gap-4 text-sm">
                <div class="p-3 rounded-lg bg-white border border-amber-100"><span class="font-semibold text-amber-800">category_slug</span><p class="text-xs text-amber-700 mt-1">Finds category by slug. Creates new one if missing.</p></div>
                <div class="p-3 rounded-lg bg-white border border-amber-100"><span class="font-semibold text-amber-800">tag_names[]</span><p class="text-xs text-amber-700 mt-1">Auto-creates tags. No pre-registration needed.</p></div>
                <div class="p-3 rounded-lg bg-white border border-amber-100"><span class="font-semibold text-amber-800">featured_image_url</span><p class="text-xs text-amber-700 mt-1">Downloads image from URL, attaches as featured media.</p></div>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="font-semibold text-gray-900 text-sm mb-3">Example: Push a Post</h3>
            <pre class="bg-gray-900 text-gray-100 rounded-xl p-3 text-xs overflow-x-auto"><code>curl -X POST https://yourdomain.com/api/v1/cms/posts \
  -H "X-API-Key: nh_your_cms_key" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Breaking News",
    "body": "&lt;p&gt;Full article...&lt;/p&gt;",
    "status": "published",
    "category_slug": "technology",
    "tag_names": ["Tech", "AI"],
    "featured_image_url": "https://example.com/photo.jpg",
    "source_url": "https://source.com/article",
    "source_name": "Partner News",
    "is_breaking": true
  }'</code></pre>
        </div>
    </div>
</section>

{{-- All Registered Endpoints --}}
<section id="endpoints" class="doc-section">
    <div class="arch-card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
            <i class="fas fa-list text-gray-600"></i> All Registered Endpoints
            <span class="text-xs text-gray-400 font-normal">({{ $routes->count() }} routes)</span>
        </h2>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200"><tr>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600 w-20">Method</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">URI</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Controller</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($routes as $route)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td>@php $methods = explode('|', $route['method']); @endphp
                            @foreach($methods as $m)
                                <span class="method-badge text-xs mr-1
                                    @if($m === 'GET') bg-green-100 text-green-700
                                    @elseif($m === 'POST') bg-blue-100 text-blue-700
                                    @elseif($m === 'PUT' || $m === 'PATCH') bg-yellow-100 text-yellow-700
                                    @elseif($m === 'DELETE') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif
                                ">{{ $m }}</span>
                            @endforeach
                        </td>
                        <td class="font-mono text-xs">{{ $route['uri'] }}</td>
                        <td class="text-xs text-gray-500">{{ $route['action'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
