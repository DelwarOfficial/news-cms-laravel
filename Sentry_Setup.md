# Sentry Error Tracking Setup

## Installation

```bash
composer require sentry/sentry-laravel
```

## Configuration

1. Create account at [sentry.io](https://sentry.io)
2. Get your DSN
3. Add to `.env`:

```env
SENTRY_LARAVEL_DSN=your-dsn-here
SENTRY_TRACES_SAMPLE_RATE=0.1
```

## Enable in Production

Edit `config/app.php` or use service provider to initialize Sentry only in production.

## Benefits

- Real-time error tracking
- Performance monitoring
- Release tracking
- Beautiful error reports with stack traces
