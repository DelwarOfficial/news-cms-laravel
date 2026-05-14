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
    <a href="#overview" class="text-blue-600 hover:text-blue-800 font-medium">Overview</a>
    <span class="text-gray-300">|</span>
    <a href="#architecture" class="text-blue-600 hover:text-blue-800 font-medium">Architecture</a>
    <span class="text-gray-300">|</span>
    <a href="#authentication" class="text-blue-600 hover:text-blue-800 font-medium">Auth</a>
    <span class="text-gray-300">|</span>
    <a href="#api-keys" class="text-blue-600 hover:text-blue-800 font-medium">API Keys</a>
    <span class="text-gray-300">|</span>
    <a href="#mobile-api" class="text-blue-600 hover:text-blue-800 font-medium">Mobile API</a>
    <span class="text-gray-300">|</span>
    <a href="#cms-api" class="text-blue-600 hover:text-blue-800 font-medium">CMS API</a>
    <span class="text-gray-300">|</span>
    <a href="#comparison" class="text-blue-600 hover:text-blue-800 font-medium">Comparison</a>
    <span class="text-gray-300">|</span>
    <a href="#quickstart" class="text-blue-600 hover:text-blue-800 font-medium">Quick Start</a>
    <span class="text-gray-300">|</span>
    <a href="#endpoints" class="text-blue-600 hover:text-blue-800 font-medium">All Endpoints</a>
</div>

{{-- 1. API Architecture Overview --}}
<section id="overview" class="doc-section mb-10">
    <div class="arch-card p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-sitemap text-indigo-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">API Architecture Overview</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="p-5 rounded-xl bg-blue-50 border border-blue-100">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-mobile-alt text-blue-600 text-lg"></i>
                    <h3 class="font-bold text-blue-900">Mobile API</h3>
                </div>
                <p class="text-sm text-blue-800 leading-relaxed">
                    Read-only public endpoints for frontend websites, mobile apps, and third-party consumers.
                    No authentication required. Returns published content only.
                </p>
                <div class="mt-3 flex gap-1.5">
                    <span class="method-badge bg-green-100 text-green-700">GET</span>
                    <span class="scope-badge bg-blue-100 text-blue-700">public</span>
                </div>
            </div>

            <div class="p-5 rounded-xl bg-amber-50 border border-amber-100">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-cloud-upload-alt text-amber-600 text-lg"></i>
                    <h3 class="font-bold text-amber-900">CMS API</h3>
                </div>
                <p class="text-sm text-amber-800 leading-relaxed">
                    Authenticated endpoints for external CMS platforms, content aggregators, and push integrations.
                    Requires API key with <code class="text-xs bg-amber-200 px-1 rounded">cms</code> scope.
                </p>
                <div class="mt-3 flex gap-1.5">
                    <span class="method-badge bg-blue-100 text-blue-700">POST</span>
                    <span class="method-badge bg-yellow-100 text-yellow-700">PUT</span>
                    <span class="method-badge bg-red-100 text-red-700">DEL</span>
                    <span class="scope-badge bg-amber-200 text-amber-800">cms</span>
                </div>
            </div>

            <div class="p-5 rounded-xl bg-purple-50 border border-purple-100">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-database text-purple-600 text-lg"></i>
                    <h3 class="font-bold text-purple-900">Shared Database</h3>
                </div>
                <p class="text-sm text-purple-800 leading-relaxed">
                    Both APIs read from and write to the same MySQL database.
                    Redis caching layer ensures fast responses. The main CMS admin panel remains unchanged.
                </p>
                <div class="mt-3 flex gap-1.5">
                    <span class="scope-badge bg-purple-100 text-purple-700">MySQL</span>
                    <span class="scope-badge bg-purple-100 text-purple-700">Redis</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 2. Architecture Diagram --}}
<section id="architecture" class="doc-section mb-10">
    <div class="arch-card p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-project-diagram text-emerald-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Architecture Diagram</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
            {{-- External Systems --}}
            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 text-center">
                    <i class="fas fa-globe text-blue-500 text-2xl mb-2"></i>
                    <div class="font-bold text-blue-900 text-sm">Mobile App</div>
                    <div class="text-xs text-blue-600">Frontend / Web</div>
                </div>
                <div class="p-4 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-200 text-center">
                    <i class="fas fa-cloud text-amber-500 text-2xl mb-2"></i>
                    <div class="font-bold text-amber-900 text-sm">External CMS</div>
                    <div class="text-xs text-amber-600">WordPress / Aggregator</div>
                </div>
            </div>

            {{-- Arrows --}}
            <div class="hidden md:flex flex-col items-center gap-8">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">GET</span>
                    <i class="fas fa-arrow-right text-blue-400 text-xl"></i>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">API Key</span>
                    <i class="fas fa-arrow-right text-amber-400 text-xl"></i>
                </div>
            </div>

            {{-- API Gateway --}}
            <div class="md:col-span-2 p-5 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-200">
                <div class="text-center mb-3">
                    <i class="fas fa-shield-alt text-gray-600 text-2xl"></i>
                    <div class="font-bold text-gray-900 mt-1">API Gateway</div>
                    <div class="text-xs text-gray-500">api/v1/*</div>
                </div>
                <div class="space-y-2 mt-3">
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Rate Limiting (60-120 req/min)</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>API Key Validation (SHA-256)</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Scope Enforcement (read/write/cms)</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Consistent {data, meta} Response Format</span>
                    </div>
                </div>
            </div>

            {{-- Arrows --}}
            <div class="hidden md:flex flex-col items-center gap-8">
                <i class="fas fa-arrow-right text-gray-400 text-xl"></i>
                <i class="fas fa-arrow-right text-gray-400 text-xl"></i>
            </div>

            {{-- Backend --}}
            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 text-center">
                    <i class="fas fa-database text-purple-500 text-2xl mb-2"></i>
                    <div class="font-bold text-purple-900 text-sm">MySQL Database</div>
                    <div class="text-xs text-purple-600">Shared Data Store</div>
                </div>
                <div class="p-4 rounded-xl bg-gradient-to-br from-rose-50 to-rose-100 border-2 border-rose-200 text-center">
                    <i class="fas fa-bolt text-rose-500 text-2xl mb-2"></i>
                    <div class="font-bold text-rose-900 text-sm">Redis Cache</div>
                    <div class="text-xs text-rose-600">5 min TTL / Invalidate on Publish</div>
                </div>
                <div class="p-4 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 border-2 border-indigo-200 text-center">
                    <i class="fas fa-cog text-indigo-500 text-2xl mb-2"></i>
                    <div class="font-bold text-indigo-900 text-sm">NewsCore CMS</div>
                    <div class="text-xs text-indigo-600">Admin Panel (unchanged)</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3. API Key Documentation --}}
<section id="api-keys" class="doc-section mb-10">
    <div class="arch-card p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-key text-amber-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">API Key Authentication</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="p-5 rounded-xl bg-gray-50 border border-gray-200">
                <h3 class="font-bold text-gray-900 mb-3">Creating an API Key</h3>
                <ol class="space-y-2 text-sm text-gray-700 list-decimal list-inside">
                    <li>Navigate to <strong>API Keys</strong> in the admin sidebar</li>
                    <li>Click <strong>Create API Key</strong></li>
                    <li>Enter a name (e.g. "Production Mobile App")</li>
                    <li>Select the required scopes</li>
                    <li>Set rate limit (default 60 req/hour)</li>
                    <li>Set expiration date (optional)</li>
                    <li>Click <strong>Create</strong></li>
                    <li><span class="text-red-600 font-bold">Copy the key immediately</span> — it won't be shown again</li>
                </ol>
            </div>

            <div class="p-5 rounded-xl bg-gray-50 border border-gray-200">
                <h3 class="font-bold text-gray-900 mb-3">Using the API Key</h3>
                <p class="text-sm text-gray-600 mb-3">Send the key in the <code class="bg-gray-200 px-1 rounded">X-API-Key</code> header:</p>
                <pre class="bg-gray-900 text-gray-100 rounded-xl p-4 text-xs overflow-x-auto"><code>curl -H "X-API-Key: nh_a1b2c3d4e5..." \
  -H "Accept: application/json" \
  http://localhost/dhaka-magazine/api/v1/posts/manage</code></pre>
                <p class="text-sm text-gray-600 mt-3">Alternative methods:</p>
                <div class="mt-2 space-y-1 text-xs text-gray-600">
                    <div><code class="bg-gray-200 px-1 rounded">Authorization: Bearer nh_xxx</code></div>
                    <div><code class="bg-gray-200 px-1 rounded">?api_key=nh_xxx</code> (query param)</div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl border border-gray-200">
                <h4 class="font-bold text-sm text-gray-900 mb-2">Available Scopes</h4>
                <div class="space-y-1.5">
                    <div><span class="scope-badge bg-green-100 text-green-700">read</span> <span class="text-xs text-gray-600">View content</span></div>
                    <div><span class="scope-badge bg-blue-100 text-blue-700">write</span> <span class="text-xs text-gray-600">Create/update content</span></div>
                    <div><span class="scope-badge bg-purple-100 text-purple-700">media</span> <span class="text-xs text-gray-600">Upload/manage media</span></div>
                    <div><span class="scope-badge bg-amber-100 text-amber-700">cms</span> <span class="text-xs text-gray-600">External push/sync</span></div>
                    <div><span class="scope-badge bg-red-100 text-red-700">admin</span> <span class="text-xs text-gray-600">Full admin access</span></div>
                    <div><span class="scope-badge bg-gray-800 text-white">*</span> <span class="text-xs text-gray-600">All scopes</span></div>
                </div>
            </div>
            <div class="p-4 rounded-xl border border-gray-200">
                <h4 class="font-bold text-sm text-gray-900 mb-2">Validation Flow</h4>
                <ol class="space-y-1.5 text-xs text-gray-600 list-decimal list-inside">
                    <li>Extract key from header/bearer/query</li>
                    <li>Check prefix matches <code class="bg-gray-200 px-1 rounded">nh_</code></li>
                    <li>Compute SHA-256 hash of the key</li>
                    <li>Look up by prefix + hash in database</li>
                    <li>Verify key is active & not expired</li>
                    <li>Check required scope</li>
                    <li>Update <code class="bg-gray-200 px-1 rounded">last_used_at</code></li>
                    <li>Attach <code class="bg-gray-200 px-1 rounded">api_key_id</code> to request</li>
                </ol>
            </div>
            <div class="p-4 rounded-xl border border-gray-200">
                <h4 class="font-bold text-sm text-gray-900 mb-2">Security Rules</h4>
                <div class="space-y-1.5 text-xs text-gray-600">
                    <div><i class="fas fa-shield-alt text-green-500 w-4"></i> Keys stored as SHA-256 hash</div>
                    <div><i class="fas fa-shield-alt text-green-500 w-4"></i> Only key prefix visible in DB</div>
                    <div><i class="fas fa-shield-alt text-green-500 w-4"></i> Full key shown only once on creation</div>
                    <div><i class="fas fa-shield-alt text-green-500 w-4"></i> Scope-based access control</div>
                    <div><i class="fas fa-shield-alt text-green-500 w-4"></i> Rate limiting per key</div>
                    <div><i class="fas fa-shield-alt text-green-500 w-4"></i> Expiration dates supported</div>
                    <div><i class="fas fa-shield-alt text-orange-500 w-4"></i> Toggle active/inactive anytime</div>
                    <div><i class="fas fa-shield-alt text-red-500 w-4"></i> Revoke = delete key from DB</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 4 & 5. Mobile API Section --}}
<section id="mobile-api" class="doc-section mb-10">
    <div class="arch-card p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-mobile-alt text-blue-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Mobile API — Read-Only Public Endpoints</h2>
        </div>
        <p class="text-sm text-gray-600 mb-6">These endpoints require <strong>no authentication</strong>. They return only published posts and active categories. Ideal for frontend websites, mobile apps, and embeddable widgets.</p>

        @php
        $mobileEndpoints = [
            ['GET', '/api/v1/posts', 'List published posts with filters', '—', 'limit, sort, category_slug, tag_slug, author_id, date_from, date_to, search, is_breaking, is_featured, is_trending, is_editors_pick, is_sticky, post_format'],
            ['GET', '/api/v1/posts/{slug}', 'Single post by slug', '—', 'Increments view_count'],
            ['GET', '/api/v1/posts/breaking', 'Latest 10 breaking posts', '—', ''],
            ['GET', '/api/v1/posts/trending', 'Latest 10 trending posts', '—', ''],
            ['GET', '/api/v1/posts/popular', 'Top 10 by view count', '—', ''],
            ['GET', '/api/v1/posts/featured', 'Latest 5 featured posts', '—', ''],
            ['GET', '/api/v1/posts/editors-pick', "Latest 5 editor's pick posts", '—', ''],
            ['POST', '/api/v1/posts/{id}/view', 'Increment view count', '—', 'Rate limited: 1/60s'],
            ['GET', '/api/v1/categories', 'All active categories (tree)', '—', ''],
            ['GET', '/api/v1/categories/{slug}/posts', 'Paginated posts for category', '—', 'limit, date_from, date_to, is_breaking, is_featured'],
            ['GET', '/api/v1/search', 'Full-text search', '—', 'q, category_slug, is_breaking, is_featured, is_trending, date_from, date_to'],
        ];
        @endphp

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm endpoint-table">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 w-20">Method</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Endpoint</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Purpose</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Scope</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Params</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($mobileEndpoints as $ep)
                    <tr>
                        <td><span class="method-badge bg-{{ $ep[0] === 'GET' ? 'green' : 'blue' }}-100 text-{{ $ep[0] === 'GET' ? 'green' : 'blue' }}-700">{{ $ep[0] }}</span></td>
                        <td class="font-mono text-xs">{{ $ep[1] }}</td>
                        <td>{{ $ep[2] }}</td>
                        <td><span class="scope-badge bg-gray-100 text-gray-600">{{ $ep[3] }}</span></td>
                        <td class="text-xs text-gray-500">{{ $ep[4] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Example Response --}}
<div class="grid md:grid-cols-2 gap-6 mb-10">
    <div class="arch-card p-6">
        <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i> Success Response
        </h3>
        <pre class="bg-gray-900 text-gray-100 rounded-xl p-4 text-xs overflow-x-auto"><code>{
  "data": [
    {
      "id": 1,
      "title": "Article Title",
      "slug": "article-title",
      "excerpt": "Summary text...",
      "image_url": "http://...",
      "published_at": "2026-05-14T12:00:00Z",
      "reading_time": 3,
      "view_count": 42,
      "is_breaking": false,
      "is_featured": true,
      "is_trending": false,
      "is_editors_pick": false,
      "is_sticky": false,
      "post_format": "standard",
      "category": "Sports",
      "category_slug": "sports",
      "author": "Author Name",
      "author_username": "author",
      "tags": ["tag1", "tag2"],
      "meta_title": "...",
      "meta_description": "...",
      "locale": "en"
    }
  ],
  "meta": {
    "page": 1,
    "limit": 15,
    "total": 145,
    "totalPages": 10
  }
}</code></pre>
    </div>

    <div class="arch-card p-6">
        <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i> Error Response
        </h3>
        <pre class="bg-gray-900 text-gray-100 rounded-xl p-4 text-xs overflow-x-auto"><code>{
  "error": "Validation Error",
  "message": "The given data was invalid.",
  "details": {
    "title": ["The title field is required."]
  }
}</code></pre>
        <div class="mt-4 space-y-2 text-xs text-gray-600">
            <div><span class="font-semibold">401</span> — Missing/invalid API key</div>
            <div><span class="font-semibold">403</span> — Insufficient scope</div>
            <div><span class="font-semibold">404</span> — Resource not found</div>
            <div><span class="font-semibold">409</span> — Conflict (has dependencies)</div>
            <div><span class="font-semibold">422</span> — Validation error</div>
            <div><span class="font-semibold">429</span> — Rate limit exceeded</div>
        </div>
    </div>
</div>

{{-- 6. CMS API Section --}}
<section id="cms-api" class="doc-section mb-10">
    <div class="arch-card p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-cloud-upload-alt text-amber-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">CMS API — External Push/Sync</h2>
        </div>
        <p class="text-sm text-gray-600 mb-6">Requires API key with <span class="scope-badge bg-amber-100 text-amber-700">cms</span> scope. Designed for external CMS platforms, WordPress plugins, and content aggregators to push content into NewsHub.</p>

        @php
        $cmsEndpoints = [
            ['GET', '/api/v1/cms/status', 'Health check', 'cms', '—'],
            ['POST', '/api/v1/cms/posts', 'Create post (auto-download image, auto-create tags)', 'cms', 'title, body, status, category_slug, tag_names[], featured_image_url, source_url, source_name, is_breaking, meta_title, meta_description'],
            ['PUT', '/api/v1/cms/posts/{id}', 'Update post', 'cms', 'Partial update of any field'],
            ['DELETE', '/api/v1/cms/posts/{id}', 'Soft-delete post (archive)', 'cms', '—'],
            ['GET', '/api/v1/cms/categories', 'List all categories', 'cms', '—'],
            ['POST', '/api/v1/cms/categories', 'Create category (slug-based, idempotent)', 'cms', 'name, slug, parent_slug'],
            ['GET', '/api/v1/cms/tags', 'List all tags', 'cms', '—'],
            ['POST', '/api/v1/cms/tags', 'Create tag (name-based, idempotent)', 'cms', 'name, slug'],
            ['POST', '/api/v1/cms/media', 'Upload media file', 'cms', 'file (multipart)'],
        ];
        @endphp

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm endpoint-table">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 w-20">Method</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Endpoint</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Purpose</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Scope</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Params</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($cmsEndpoints as $ep)
                    <tr>
                        <td><span class="method-badge bg-{{ $ep[0] === 'GET' ? 'green' : ($ep[0] === 'POST' ? 'blue' : ($ep[0] === 'PUT' ? 'yellow' : 'red')) }}-100 text-{{ $ep[0] === 'GET' ? 'green' : ($ep[0] === 'POST' ? 'blue' : ($ep[0] === 'PUT' ? 'yellow' : 'red')) }}-700">{{ $ep[0] }}</span></td>
                        <td class="font-mono text-xs">{{ $ep[1] }}</td>
                        <td>{{ $ep[2] }}</td>
                        <td><span class="scope-badge bg-amber-100 text-amber-700">{{ $ep[3] }}</span></td>
                        <td class="text-xs text-gray-500">{{ $ep[4] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 p-5 rounded-xl bg-amber-50 border border-amber-200">
            <h4 class="font-bold text-amber-900 text-sm mb-3 flex items-center gap-2">
                <i class="fas fa-star text-amber-500"></i> CMS Post Creation — Smart Features
            </h4>
            <div class="grid md:grid-cols-3 gap-4 text-sm">
                <div class="p-3 rounded-lg bg-white border border-amber-100">
                    <div class="font-semibold text-amber-800">category_slug</div>
                    <div class="text-xs text-amber-700 mt-1">Resolves existing category by slug. Auto-creates if not found.</div>
                </div>
                <div class="p-3 rounded-lg bg-white border border-amber-100">
                    <div class="font-semibold text-amber-800">tag_names[]</div>
                    <div class="text-xs text-amber-700 mt-1">Auto-creates tags via <code class="bg-amber-100 px-1 rounded">firstOrCreate</code>. No need to pre-register tags.</div>
                </div>
                <div class="p-3 rounded-lg bg-white border border-amber-100">
                    <div class="font-semibold text-amber-800">featured_image_url</div>
                    <div class="text-xs text-amber-700 mt-1">Downloads image via HTTP, stores in Media library, attaches as featured.</div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <h4 class="font-bold text-gray-900 text-sm mb-3">Example: Push a post from external CMS</h4>
            <pre class="bg-gray-900 text-gray-100 rounded-xl p-4 text-xs overflow-x-auto"><code>curl -X POST http://localhost/dhaka-magazine/api/v1/cms/posts \
  -H "X-API-Key: nh_your_cms_key" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Breaking News from Partner",
    "body": "<p>Full article content here...</p>",
    "status": "published",
    "category_slug": "technology",
    "tag_names": ["Tech", "AI", "Innovation"],
    "featured_image_url": "https://example.com/photo.jpg",
    "source_url": "https://original-source.com/article",
    "source_name": "Partner News Agency",
    "is_breaking": true,
    "meta_title": "SEO Optimized Title",
    "meta_description": "SEO meta description"
  }'</code></pre>
        </div>
    </div>
</section>

{{-- 7. Mobile API Auth Section --}}
<section id="authentication" class="doc-section mb-10">
    <div class="arch-card p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-exchange-alt text-green-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Authentication Flow</h2>
        </div>

        <div class="relative">
            {{-- Flow Steps --}}
            <div class="grid gap-6 md:grid-cols-6">
                @php
                $steps = [
                    ['icon' => 'fa-key', 'color' => 'amber', 'title' => 'Admin creates API Key', 'desc' => 'Via admin panel API Keys section'],
                    ['icon' => 'fa-check-double', 'color' => 'green', 'title' => 'Scopes selected', 'desc' => 'read/write/media/cms/admin'],
                    ['icon' => 'fa-copy', 'color' => 'blue', 'title' => 'Key copied once', 'desc' => 'Shown only once, stored as SHA-256'],
                    ['icon' => 'fa-paper-plane', 'color' => 'purple', 'title' => 'Request sent with key', 'desc' => 'X-API-Key header or Bearer token'],
                    ['icon' => 'fa-shield-alt', 'color' => 'red', 'title' => 'Middleware validates', 'desc' => 'Hash match + scope check + rate limit'],
                    ['icon' => 'fa-check-circle', 'color' => 'green', 'title' => 'JSON response returned', 'desc' => '{data, meta} format'],
                ];
                @endphp

                @foreach($steps as $i => $step)
                <div class="text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-{{ $step['color'] }}-100 border-2 border-{{ $step['color'] }}-200 flex items-center justify-center mb-3">
                        <i class="fas {{ $step['icon'] }} text-{{ $step['color'] }}-600 text-xl"></i>
                    </div>
                    <div class="text-xs font-bold text-gray-900">{{ $step['title'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $step['desc'] }}</div>
                    @if($i < count($steps) - 1)
                    <div class="hidden md:block mt-2">
                        <i class="fas fa-arrow-down text-gray-300 text-lg"></i>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- 8. Key Differences Table --}}
<section id="comparison" class="doc-section mb-10">
    <div class="arch-card p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-balance-scale text-red-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Key Differences: Mobile API vs CMS API</h2>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Feature</th>
                        <th class="text-left py-3 px-4 font-semibold text-blue-700 bg-blue-50">Mobile API</th>
                        <th class="text-left py-3 px-4 font-semibold text-amber-700 bg-amber-50">CMS API</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                    $comparisons = [
                        ['Purpose', 'Read-only content delivery', 'Content push/sync from external systems'],
                        ['Auth Scope', 'None (public)', 'cms scope required'],
                        ['Read Permission', 'Published posts only', 'All statuses (including drafts via manage)'],
                        ['Write Permission', 'No write (except view count)', 'Full CRUD (create, update, soft-delete)'],
                        ['Status Access', 'published only', 'draft, pending, published, scheduled, archived'],
                        ['Soft Delete', 'N/A', 'Archived + deleted_at on DELETE'],
                        ['SEO Support', 'Read-only meta fields', 'Full SEO: meta_title, meta_description, canonical_url, og_image'],
                        ['Media Handling', 'Read image URLs', 'Upload multipart + auto-download from URL'],
                        ['Category Resolution', 'By slug', 'By slug with auto-create fallback'],
                        ['Tag Handling', 'Read tags', 'tag_names[] auto-create via firstOrCreate'],
                        ['Rate Limit', '60 req/min', '60 req/min (cms scope)'],
                        ['Use Case', 'Mobile app, website, SPA', 'WordPress plugin, aggregator, CMS sync'],
                    ];
                    @endphp
                    @foreach($comparisons as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-semibold text-gray-700">{{ $row[0] }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600 bg-blue-50/30">{{ $row[1] }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600 bg-amber-50/30">{{ $row[2] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- 9. Quick Start Guide --}}
<section id="quickstart" class="doc-section mb-10">
    <div class="arch-card p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-rocket text-green-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Quick Start Guide</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">1</div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Create an API Key</h4>
                        <p class="text-xs text-gray-600 mt-1">Go to <strong>API Keys</strong> in the sidebar → <strong>Create API Key</strong>. Name it, select scopes, set limits.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Copy the Key</h4>
                        <p class="text-xs text-gray-600 mt-1">The key is shown <strong class="text-red-600">only once</strong>. Store it securely. It starts with <code class="bg-gray-200 px-1 rounded">nh_</code>.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">3</div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Make Your First Request</h4>
                        <pre class="bg-gray-900 text-gray-100 rounded-xl p-3 text-xs mt-2 overflow-x-auto"><code>curl http://localhost/dhaka-magazine/api/v1/posts

# With API key:
curl -H "X-API-Key: nh_your_key" \
  http://localhost/dhaka-magazine/api/v1/dashboard</code></pre>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">4</div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Check the Response</h4>
                        <p class="text-xs text-gray-600 mt-1">All responses follow the <code class="bg-gray-200 px-1 rounded">{data, meta}</code> format. Lists include pagination metadata.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">5</div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Integrate with Mobile App</h4>
                        <p class="text-xs text-gray-600 mt-1">Use the public Mobile API endpoints (no auth) to fetch posts, categories, and search results directly in your mobile app or frontend.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">6</div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Integrate with External CMS</h4>
                        <p class="text-xs text-gray-600 mt-1">Use the CMS API with <code class="bg-gray-200 px-1 rounded">cms</code> scope to push content from WordPress, Drupal, or any content aggregator.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 p-5 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200">
            <div class="flex items-center gap-3">
                <i class="fas fa-lightbulb text-blue-500 text-xl"></i>
                <div>
                    <h4 class="font-bold text-blue-900 text-sm">Tip</h4>
                    <p class="text-xs text-blue-800">Create separate API keys for each integration (mobile app, CMS sync, analytics). If one key is compromised, revoke only that key without affecting other integrations.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 10. All Registered Endpoints --}}
<section id="endpoints" class="doc-section">
    <div class="arch-card p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-list text-gray-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">All Registered V1 Endpoints</h2>
            <span class="text-xs text-gray-400">({{ $routes->count() }} routes)</span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm endpoint-table">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 w-20">Method</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">URI</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($routes as $route)
                    <tr>
                        <td>
                            @php
                                $methods = explode('|', $route['method']);
                            @endphp
                            @foreach($methods as $m)
                                <span class="method-badge text-xs mr-1
                                    @if($m === 'GET') bg-green-100 text-green-700
                                    @elseif($m === 'POST') bg-blue-100 text-blue-700
                                    @elseif($m === 'PUT' || $m === 'PATCH') bg-yellow-100 text-yellow-700
                                    @elseif($m === 'DELETE') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif
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
