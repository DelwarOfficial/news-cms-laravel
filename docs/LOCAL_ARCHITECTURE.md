# Dhaka Magazine Local Architecture

## Local entry points

- Backend/CMS Laravel app: `D:\laragon\dhaka-magazine\backend`
- Public web root for the backend: `D:\laragon\dhaka-magazine\backend\public`
- Laragon compatibility shim: `D:\laragon\www\dhaka-magazine\index.php`

For the cleanest Apache setup, point the virtual host document root directly to:

```apache
DocumentRoot "D:/laragon/dhaka-magazine/backend/public"
<Directory "D:/laragon/dhaka-magazine/backend/public">
    AllowOverride All
    Require all granted
</Directory>
```

The `D:\laragon\www\dhaka-magazine` shim is only for legacy Laragon URLs. Avoid editing application code there.

## Local URLs

- CMS login: `/login`
- CMS dashboard: `/admin`
- Public REST API (v1): `/api/v1/posts`, `/api/v1/categories`
- Frontend URLs: served by the separate frontend repo (news-cms-frontend)

The CMS owns content, users, media, categories, placements, settings, and moderation. It does NOT render any frontend Blade views. The separate frontend repo reads content exclusively through the public REST API.

## Route boundary

The article preview redirect (web.php) proxies to the external frontend URL configured in `FRONTEND_URL`. No catch-all frontend routes exist in this CMS repo.

## Redis, cache, session, and queue

This project uses Predis from Composer, so local `.env` should include:

```dotenv
CACHE_STORE=redis
SESSION_DRIVER=redis
SESSION_STORE=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

Run a queue worker for background jobs:

```powershell
php artisan queue:work redis --tries=3
```

## Deployment checklist

Run these after moving the project or changing `.env`:

```powershell
php artisan optimize:clear
php artisan migrate --force
php artisan storage:link
php artisan permission:cache-reset
php artisan route:list
```

If a moved project still references an old path in `storage/logs/laravel.log`, clear Laravel bootstrap/cache files and rerun `composer dump-autoload`.

## Query and scaling rules

- Public post feeds should use `Post::withContentRelations()` before rendering article arrays.
- Homepage, popular news, ticker headlines, and placement output should be cached and invalidated from model observers.
- Admin tables should eager-load author/category/media relationships before Blade loops.
- Add indexes for frequently combined filters: `status + published_at`, placement key/activity/order, category pivots, flags, and location IDs.
- Future SaaS separation should happen at service/API boundaries: keep CMS writes in admin controllers and expose frontend reads through stable read services or API resources.
