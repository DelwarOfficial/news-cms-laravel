# Queue + Redis Setup (Production Ready)

## Why Use Queue + Redis?

- Heavy tasks (image processing, email sending, sitemap generation) should not block the user
- Better performance and user experience
- Scalable architecture

## Installation

```bash
composer require predis/predis
```

## Configuration

### 1. Update `.env`

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2. Run Queue Worker

```bash
php artisan queue:work
```

### 3. Use in Code (Example)

```php
use App\Jobs\ProcessPostPublishing;

// When publishing a post
ProcessPostPublishing::dispatch($post);
```

## Example Jobs Created

- `ProcessPostPublishing.php` — Handles post publishing tasks

## Production Tips

- Use **Supervisor** to keep queue workers running
- Monitor failed jobs with `php artisan queue:failed`
- Retry failed jobs: `php artisan queue:retry all`

---

**Recommended for Production**
