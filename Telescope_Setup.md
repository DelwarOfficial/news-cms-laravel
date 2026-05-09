# Laravel Telescope Setup

## Installation

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

## Configuration

Add to `.env`:

```env
TELESCOPE_ENABLED=true
TELESCOPE_PATH=telescope
```

## Access

- Local: `http://127.0.0.1:8000/telescope`
- Only accessible in local/development environment by default

## Production Note

For production, you can enable it with proper authorization in `TelescopeServiceProvider.php`.
