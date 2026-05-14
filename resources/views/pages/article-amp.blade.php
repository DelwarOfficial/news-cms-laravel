<!doctype html>
<html amp lang="bn">
<head>
  <meta charset="utf-8">
  <title>{{ $article['title'] }} - Dhaka Magazine</title>
  <link rel="canonical" href="{{ $canonicalUrl }}">
  <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
  <meta name="description" content="{{ $article['meta_description'] ?? $article['excerpt'] ?? '' }}">
  <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>
  <noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
  <style amp-custom>
    body{font-family:system-ui,-apple-system,BlinkMacSystemFont,"Noto Sans Bengali",sans-serif;margin:0;background:#fff;color:#111827;line-height:1.75}
    header,main,footer{max-width:760px;margin:0 auto;padding:16px}
    header{border-bottom:1px solid #e5e7eb}
    .brand{font-weight:800;color:#e2231a;text-decoration:none}
    .category{color:#e2231a;font-weight:700;text-decoration:none;font-size:14px}
    h1{font-size:34px;line-height:1.25;margin:12px 0}
    .meta{font-size:14px;color:#6b7280;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;padding:10px 0;margin:18px 0}
    .excerpt{font-size:18px;font-weight:700;color:#111827}
    figure{margin:0 0 22px}
    figcaption{font-size:12px;color:#6b7280;margin-top:6px}
    p{font-size:18px;margin:0 0 18px}
    blockquote{border-left:4px solid #e2231a;margin:20px 0;padding-left:16px;color:#374151}
    footer{border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px}
  </style>
  <script async src="https://cdn.ampproject.org/v0.js"></script>
  <script type="application/ld+json">
  {!! json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'NewsArticle',
      'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonicalUrl],
      'headline' => $article['title'],
      'description' => $article['meta_description'] ?? $article['excerpt'] ?? '',
      'image' => array_values(array_filter([$article['og_image'] ?? $article['image_url'] ?? null])),
      'datePublished' => optional($article['published_at'] ?? null)->toIso8601String() ?: now()->toIso8601String(),
      'dateModified' => optional($article['updated_at'] ?? null)->toIso8601String() ?: optional($article['published_at'] ?? null)->toIso8601String() ?: now()->toIso8601String(),
      'author' => ['@type' => 'Person', 'name' => $article['author'] ?? 'Dhaka Magazine Desk'],
      'publisher' => [
          '@type' => 'Organization',
          'name' => 'Dhaka Magazine',
          'logo' => ['@type' => 'ImageObject', 'url' => asset('images/dhaka-magazine-color-logo.svg')],
      ],
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
  </script>
</head>
<body>
  <header>
    <a class="brand" href="{{ route('home') }}">Dhaka Magazine</a>
  </header>

  <main>
    <a class="category" href="{{ $article['category_url'] ?? route('category.parent', $article['category_slug'] ?? 'bangladesh') }}">{{ $article['category'] ?? 'News' }}</a>

    @if(!empty($article['shoulder']))
      <div class="category">{{ $article['shoulder'] }}</div>
    @endif

    <h1>{{ $article['title'] }}</h1>

    <div class="meta">
      @if($article['show_author'] ?? true)
        <strong>{{ $article['author'] ?? 'Dhaka Magazine Desk' }}</strong>
      @endif
      @if($article['show_publish_date'] ?? true)
        <span> | {{ $article['date'] ?? '' }}</span>
      @endif
    </div>

    @if(!empty($article['image_url']))
      <figure>
        <amp-img src="{{ $article['image_url'] }}" width="1200" height="675" layout="responsive" alt="{{ $article['image_alt'] ?? $article['title'] }}"></amp-img>
        @if(!empty($article['image_caption']))
          <figcaption>{{ $article['image_caption'] }}</figcaption>
        @endif
      </figure>
    @endif

    @if(!empty($article['excerpt']))
      <p class="excerpt">{{ $article['excerpt'] }}</p>
    @endif

    @foreach(($article['body'] ?? []) as $paragraph)
      <p>{{ $paragraph }}</p>
    @endforeach
  </main>

  <footer>
    <a href="{{ $canonicalUrl }}">View full article</a>
  </footer>
</body>
</html>
