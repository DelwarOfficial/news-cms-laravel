# Step 5: Production Readiness — Complete Implementation Guide

> **Status:** Ready for implementation  
> **Updated:** May 2026  
> **Scope:** Database, Cache, Queue, Storage, Rate Limiting

---

## 📋 Checklist Overview

- [ ] Switch to MySQL
- [ ] Set up Redis (caching & queues)
- [ ] Configure Queue (heavy jobs)
- [ ] Set up Horizon (optional but recommended)
- [ ] Configure proper storage (S3 or local with backup)
- [ ] Add rate limiting on public routes
- [ ] Set `ENABLE_FALLBACK_CONTENT=false` in production

---

## 1️⃣ Switch to MySQL

### Why MySQL?
- Production-grade relational database
- Better than SQLite for concurrent connections
- Supports backups, replication, clustering
- Better for scaling

### Configuration

#### Update `.env` for Production:

```env
DB_CONNECTION=mysql
DB_HOST=your-db-host.example.com
DB_PORT=3306
DB_DATABASE=newscore_production
DB_USERNAME=newscore_user
DB_PASSWORD=your-secure-password-here
```

#### For Local Development (keep SQLite for testing):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=newscore_local
DB_USERNAME=root
DB_PASSWORD=root
```

### Creating the Database

```bash
# On your production server
mysql -u root -p
CREATE DATABASE newscore_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'newscore_user'@'localhost' IDENTIFIED BY 'your-secure-password';
GRANT ALL PRIVILEGES ON newscore_production.* TO 'newscore_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Running Migrations

```bash
# In production environment
php artisan migrate --force

# If you need to seed initial data
php artisan db:seed --force
```

### Verification

```bash
php artisan tinker
>>> DB::connection('mysql')->select('SELECT 1');
>>> exit
```

---

## 2️⃣ Set Up Redis

### Installation

#### On Ubuntu/Debian:
```bash
# Install Redis server
sudo apt-get install redis-server

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Verify Redis is running
redis-cli ping  # Should return: PONG
```

#### On macOS (with Homebrew):
```bash
brew install redis
brew services start redis
redis-cli ping
```

#### Using Docker (Recommended for Production):
```bash
docker pull redis:latest
docker run -d --name newscore-redis -p 6379:6379 redis:latest
docker exec newscore-redis redis-cli ping
```

### Laravel Configuration

#### Install Predis (Redis PHP client):

```bash
composer require predis/predis
```

#### Update `.env`:

```env
# Cache Configuration
CACHE_STORE=redis
CACHE_TTL=3600

# Session Configuration
SESSION_DRIVER=redis

# Queue Configuration
QUEUE_CONNECTION=redis

# Redis Connection
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=predis

# For production, set a password
REDIS_PASSWORD=your-secure-redis-password
```

#### Update `config/cache.php` (if needed):

The default configuration already includes Redis stores. Verify:

```php
'default' => env('CACHE_STORE', 'redis'),

'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
    ],
    'redis_cache' => [
        'driver' => 'redis',
        'connection' => 'cache',
    ],
],
```

### Redis Best Practices for Production

```env
# Set appropriate database numbers
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_QUEUE_DB=2

# Add prefix for multiple environments
REDIS_PREFIX=newscore_prod_

# Set password
REDIS_PASSWORD=your-secure-password-here
```

### Monitoring Redis

```bash
# Check Redis info
redis-cli info

# Monitor commands in real-time
redis-cli monitor

# Check key count
redis-cli dbsize

# Clear all keys (use with caution!)
redis-cli FLUSHDB
```

---

## 3️⃣ Configure Queue

### Why Queue?
- Heavy tasks (image processing, emails, sitemap generation) shouldn't block users
- Better scalability and user experience
- Can retry failed jobs

### Configuration

#### Update `.env`:

```env
QUEUE_CONNECTION=redis
QUEUE_FAILED_TABLE=failed_jobs
```

#### Ensure Database Setup:

```bash
# Create failed_jobs table
php artisan queue:failed-table
php artisan migrate
```

### Using Queue in Code

#### Example 1: Dispatch Job

```php
// In a Controller
use App\Jobs\ProcessPostPublishing;

public function publish(Post $post)
{
    // Dispatch job to queue
    ProcessPostPublishing::dispatch($post);
    
    return response()->json(['message' => 'Publishing...']);
}
```

#### Example 2: Create a Job

```bash
php artisan make:job ProcessPostPublishing
```

```php
// app/Jobs/ProcessPostPublishing.php
namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPostPublishing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        // Heavy processing here
        // - Generate thumbnail
        // - Create SEO metadata
        // - Send notifications
        // - Clear cache
        
        $this->post->update(['processing_status' => 'completed']);
    }

    // Retry on failure
    public function retryUntil()
    {
        return now()->addMinutes(10);
    }
}
```

### Running Queue Worker

#### For Development:

```bash
# Single worker (foreground)
php artisan queue:work redis --tries=3

# With timeout
php artisan queue:work redis --timeout=300 --tries=3

# With memory limit
php artisan queue:work redis --memory=512 --timeout=300
```

#### For Production (using Supervisor):

```bash
# Install Supervisor
sudo apt-get install supervisor

# Create Supervisor config
sudo nano /etc/supervisor/conf.d/newscore-queue.conf
```

```ini
[program:newscore-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/newscore/artisan queue:work redis --sleep=3 --tries=3 --timeout=300
autostart=true
autorestart=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/newscore-queue.log
stopwaitsecs=3600
```

```bash
# Update and restart Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start newscore-queue:*

# Check status
sudo supervisorctl status newscore-queue:*

# View logs
tail -f /var/log/newscore-queue.log
```

### Queue Monitoring

```bash
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Retry specific job
php artisan queue:retry <id>

# Clear failed jobs
php artisan queue:flush
```

---

## 4️⃣ Set Up Horizon (Optional but Recommended)

### Why Horizon?
- Beautiful dashboard for queue monitoring
- Real-time job metrics
- Failed job tracking
- Worker management

### Installation

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

### Configuration

Edit `config/horizon.php`:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'simple',
            'processes' => 4,  // Number of queue workers
            'tries' => 3,
            'timeout' => 300,
            'memory' => 64,
        ],
    ],
],
```

### Running Horizon

```bash
# Foreground (development)
php artisan horizon

# Production (with Supervisor)
# See Supervisor config below
```

### Supervisor Configuration for Horizon

```ini
[program:newscore-horizon]
process_name=%(program_name)s
command=php /path/to/newscore/artisan horizon
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/newscore-horizon.log
stopwaitsecs=3600
```

### Accessing the Dashboard

Navigate to: `https://your-domain.com/horizon`

Configure authentication in `app/Providers/HorizonServiceProvider.php`:

```php
protected function gate()
{
    Gate::define('viewHorizon', function ($user) {
        return in_array($user->email, [
            'admin@newscore.com',
        ]);
    });
}
```

---

## 5️⃣ Configure Proper Storage

### Option A: Local Storage with Regular Backups (Recommended for Small-Medium)

#### Update `.env`:

```env
FILESYSTEM_DISK=local
APP_STORAGE_PATH=/storage
```

#### Create Backup Script

Create `scripts/backup-storage.sh`:

```bash
#!/bin/bash

# Variables
STORAGE_PATH="/path/to/newscore/storage/app"
BACKUP_PATH="/backups/newscore"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_PATH/storage_$DATE.tar.gz"

# Create backup directory
mkdir -p $BACKUP_PATH

# Create compressed backup
tar -czf $BACKUP_FILE $STORAGE_PATH

# Keep only last 7 days of backups
find $BACKUP_PATH -name "storage_*.tar.gz" -mtime +7 -delete

# Log
echo "Backup created: $BACKUP_FILE" >> /var/log/newscore-backups.log

# Upload to remote (optional)
# aws s3 cp $BACKUP_FILE s3://your-bucket/backups/
```

#### Make it Executable and Add to Crontab

```bash
chmod +x scripts/backup-storage.sh

# Add to crontab
crontab -e

# Add this line (daily at 2 AM)
0 2 * * * /path/to/newscore/scripts/backup-storage.sh
```

### Option B: S3 Cloud Storage (Recommended for Large-Scale)

#### Installation

```bash
composer require aws/aws-sdk-php
```

#### Update `.env`:

```env
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-newscore-bucket
AWS_URL=https://your-newscore-bucket.s3.amazonaws.com
AWS_VISIBILITY=public
```

#### Create S3 Bucket (AWS CLI)

```bash
aws s3api create-bucket \
    --bucket newscore-production \
    --region us-east-1

# Enable versioning
aws s3api put-bucket-versioning \
    --bucket newscore-production \
    --versioning-configuration Status=Enabled

# Block public access
aws s3api put-public-access-block \
    --bucket newscore-production \
    --public-access-block-configuration \
    "BlockPublicAcls=true,IgnorePublicAcls=true,BlockPublicPolicy=true,RestrictPublicBuckets=true"
```

#### Update Application Code for S3

```php
// In Controllers
use Illuminate\Support\Facades\Storage;

// Upload file to S3
$path = Storage::disk('s3')->put('uploads/posts', $file);

// Get public URL
$url = Storage::disk('s3')->url($path);

// Delete file
Storage::disk('s3')->delete($path);
```

#### Set Up CloudFront CDN (Optional)

```php
// config/filesystems.php or in model
public function getImageUrlAttribute()
{
    if ($this->file_path) {
        return config('filesystems.disks.s3.cdn_url') . '/' . $this->file_path;
    }
    return null;
}
```

#### S3 Lifecycle Rules for Auto-Cleanup

```bash
# Create lifecycle.json
cat > lifecycle.json << EOF
{
  "Rules": [
    {
      "Id": "DeleteOldFiles",
      "Status": "Enabled",
      "Prefix": "uploads/",
      "Expiration": {
        "Days": 365
      }
    },
    {
      "Id": "DeleteOldVersions",
      "NoncurrentVersionExpiration": {
        "NoncurrentDays": 30
      }
    }
  ]
}
EOF

# Apply to bucket
aws s3api put-bucket-lifecycle-configuration \
    --bucket newscore-production \
    --lifecycle-configuration file://lifecycle.json
```

---

## 6️⃣ Add Rate Limiting on Public Routes

### Current Status
- ✅ Admin login has rate limiting (5 attempts per 15 min)
- ⚠️ Public API endpoints need rate limiting

### Implementation

#### Update Routes with Throttle Middleware

Edit `routes/api.php`:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostApiController;
// ... other imports

// PUBLIC ENDPOINTS - WITH RATE LIMITING
Route::middleware('throttle:100,1')->group(function () {
    Route::get('/posts', [PostApiController::class, 'index']);
    Route::get('/posts/{slug}', [PostApiController::class, 'show']);
    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/categories/{slug}/posts', [CategoryApiController::class, 'posts']);
    Route::get('/tags/{slug}/posts', [TagApiController::class, 'posts']);
    Route::get('/search', [SearchApiController::class, 'index']);
    Route::get('/trending', [PostApiController::class, 'trending']);
    Route::get('/breaking', [PostApiController::class, 'breaking']);
    Route::get('/featured', [PostApiController::class, 'featured']);
});

// STRICTER RATE LIMIT FOR LOGIN
Route::post('/auth/login', [AuthApiController::class, 'login'])
    ->middleware('throttle:5,15');  // 5 attempts per 15 minutes

// AUTHENTICATED ROUTES - HIGHER LIMIT
Route::middleware(['auth:sanctum', 'throttle:300,1'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/me', [AuthApiController::class, 'me']);
    });
    // ... authenticated routes
});
```

#### Create Custom Rate Limiter (Optional)

Edit `app/Providers/RouteServiceProvider.php` or add to `bootstrap/app.php`:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
});

RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->email.'|'.$request->ip());
});

RateLimiter::for('search', function (Request $request) {
    return Limit::perMinute(30)->by($request->ip());
});
```

#### Update Routes to Use Custom Limiters

```php
Route::post('/auth/login', [AuthApiController::class, 'login'])
    ->middleware('throttle:login');

Route::get('/search', [SearchApiController::class, 'index'])
    ->middleware('throttle:search');
```

#### Rate Limit Response

When limit is exceeded, user gets:

```json
{
  "message": "Too Many Requests",
  "errors": {
    "RateLimitExceeded": "Rate limit exceeded. Try again in 60 seconds."
  }
}
```

### Frontend Routes Rate Limiting

Edit `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES - MODERATE RATE LIMIT
Route::middleware('throttle:100,1')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/article/{slug}', [PostController::class, 'show'])->name('article.show');
    Route::get('/category/{parentSlug}', [CategoryController::class, 'showParent'])->name('category.parent');
});

// API-like endpoints
Route::post('/posts/{post}/view', [PostController::class, 'incrementView'])
    ->middleware('throttle:600,1'); // Allow 600 view increments per minute
```

---

## 7️⃣ Set ENABLE_FALLBACK_CONTENT=false in Production

### Current Status
- ✅ Fallback content system is implemented
- ⚠️ Needs to be disabled in production

### Configuration

#### Update `.env` by Environment

**Development/Local (.env.local):**
```env
ENABLE_FALLBACK_CONTENT=true
DEMO_FALLBACK_ENABLED=true
```

**Production (.env.production):**
```env
ENABLE_FALLBACK_CONTENT=false
DEMO_FALLBACK_ENABLED=false
```

#### Verify in Code

The fallback is checked in `config/homepage.php`:

```php
'demo_fallback' => [
    'enabled' => env('ENABLE_FALLBACK_CONTENT', env('DEMO_FALLBACK_ENABLED', false)),
],
```

And used in `app/Support/FallbackDataService.php`:

```php
public static function centralized(): array
{
    if (! config('homepage.demo_fallback.enabled', true)) {
        return []; // Returns empty array in production
    }
    // ... return fallback data
}
```

#### Create Environment-Specific Files

```bash
# Create production-specific files
cp .env .env.production
cp .env .env.local

# Edit production
nano .env.production
# Set: ENABLE_FALLBACK_CONTENT=false

# Edit local  
nano .env.local
# Set: ENABLE_FALLBACK_CONTENT=true
```

#### Deployment Script

Create `deploy.sh`:

```bash
#!/bin/bash

ENV=$1

if [ "$ENV" = "production" ]; then
    cp .env.production .env
    echo "✅ Production environment activated"
    echo "ENABLE_FALLBACK_CONTENT=$(grep ENABLE_FALLBACK_CONTENT .env)"
elif [ "$ENV" = "local" ]; then
    cp .env.local .env
    echo "✅ Local environment activated"
else
    echo "Usage: ./deploy.sh [production|local]"
    exit 1
fi

php artisan config:clear
php artisan cache:clear
echo "✅ Configuration cache cleared"
```

```bash
chmod +x deploy.sh
./deploy.sh production
```

---

## 🚀 Production Deployment Sequence

### Pre-Deployment Checklist

```bash
# 1. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 2. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Create storage link (if using local storage)
php artisan storage:link

# 4. Run migrations
php artisan migrate --force --step

# 5. Seed required data
php artisan db:seed --force

# 6. Test queue
php artisan queue:work redis --tries=1 --max-time=10

# 7. Verify key services
php artisan tinker
>>> DB::connection()->getPDO();  // Test DB
>>> Cache::put('test', 'value', 60); // Test cache
>>> exit
```

### Environment Variables Summary

```env
# ===== PRODUCTION SETTINGS =====

# App
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxxxxxxxxxxxx  # Generate with: php artisan key:generate

# Database - MySQL
DB_CONNECTION=mysql
DB_HOST=db-host.example.com
DB_PORT=3306
DB_DATABASE=newscore_production
DB_USERNAME=newscore_user
DB_PASSWORD=secure-password

# Cache & Session
CACHE_STORE=redis
SESSION_DRIVER=redis

# Queue
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=redis-host.example.com
REDIS_PORT=6379
REDIS_PASSWORD=redis-password
REDIS_CLIENT=predis

# Storage
FILESYSTEM_DISK=local  # or s3
AWS_BUCKET=newscore-production  # If using S3
AWS_REGION=us-east-1
AWS_ACCESS_KEY_ID=xxxx
AWS_SECRET_ACCESS_KEY=xxxx

# Fallback Content
ENABLE_FALLBACK_CONTENT=false

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=xxxxx
MAIL_PASSWORD=xxxxx

# Error Tracking (Optional)
SENTRY_LARAVEL_DSN=https://xxxxx@sentry.io/xxxxx

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### Post-Deployment Verification

```bash
# 1. Verify database
php artisan tinker
>>> Post::count();
>>> Category::count();

# 2. Check Redis connectivity
redis-cli ping  # Should return: PONG

# 3. Test queue worker
php artisan queue:work redis --tries=1 --max-time=5

# 4. Verify cache
php artisan cache:clear
php artisan cache:test

# 5. Test API endpoints
curl https://your-domain.com/api/posts
curl https://your-domain.com/api/categories

# 6. Check logs
tail -f storage/logs/laravel.log
```

---

## 📊 Monitoring & Maintenance

### Daily Tasks
- Monitor error logs: `tail -f storage/logs/laravel.log`
- Check queue status: `php artisan queue:failed`
- Monitor Redis: `redis-cli info`
- Check disk space: `df -h`

### Weekly Tasks
- Review failed jobs: `php artisan queue:failed`
- Check database size: `SELECT pg_database.datname, pg_size_pretty(pg_database_size(pg_database.datname)) FROM pg_database ORDER BY pg_database_size(pg_database.datname) DESC;`
- Verify backups completed

### Monthly Tasks
- Update dependencies: `composer update`
- Optimize database: `php artisan optimize`
- Prune cache: `php artisan cache:prune`
- Prune failed jobs: `php artisan queue:prune-failed`

### Commands Reference

```bash
# Queue Management
php artisan queue:work                    # Start worker
php artisan queue:failed                  # List failed jobs
php artisan queue:retry all               # Retry all failed
php artisan queue:flush                   # Clear queue

# Cache Management  
php artisan cache:clear                   # Clear all cache
php artisan cache:prune                   # Remove expired entries
php artisan cache:forget key              # Forget specific key

# Database Management
php artisan migrate                       # Run migrations
php artisan db:seed                       # Seed database
php artisan db:wipe                       # Wipe database
php artisan backup:run                    # Run backup (if using spatie/laravel-backup)

# Optimization
php artisan optimize                      # Optimize application
php artisan config:cache                  # Cache configuration
php artisan route:cache                   # Cache routes
php artisan view:cache                    # Cache views
```

---

## 🔗 Related Documentation

- [MySQL Setup Guide](./docs/mysql-setup.md) ← Create if needed
- [Redis Best Practices](./docs/redis-best-practices.md) ← Create if needed
- [Queue Worker Monitoring](./docs/queue-monitoring.md) ← Create if needed
- [S3 Integration Guide](./docs/s3-integration.md) ← Create if needed
- [Backup Strategy](./docs/backup-strategy.md) ← Create if needed

---

**✅ Production Readiness: Complete**

This guide covers all aspects of production deployment. Execute in order for optimal results.

*Last Updated: May 2026*
