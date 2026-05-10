# RBAC Implementation Guide

This project uses Spatie Laravel Permission for the News CMS role and permission system.

## Canonical Roles

- Super Admin
- Admin
- Editor
- Author/Reporter
- Contributor

## Permission Matrix

Permissions are defined in `app/Services/Rbac/PermissionService.php` and seeded by `database/seeders/RolePermissionSeeder.php`.

| Permission | Author/Reporter | Editor | Contributor | Admin | Super Admin |
| --- | --- | --- | --- | --- | --- |
| `posts.create` | Yes | Yes | Yes | Yes | Yes |
| `posts.edit.own` | Yes | Yes | Yes | Yes | Yes |
| `posts.submit_review` | Yes | No | Yes | No | No |
| `posts.edit.any` | No | Yes | No | Yes | Yes |
| `posts.delete.own` | Yes | Yes | No | Yes | Yes |
| `posts.delete.any` | No | Yes | No | Yes | Yes |
| `posts.publish` | No | Yes | No | Yes | Yes |
| `categories.manage` | No | Yes | No | Yes | Yes |
| `tags.manage` | Yes | Yes | Yes | Yes | Yes |
| `media.manage` | Yes | Yes | Yes | Yes | Yes |
| `comments.manage` | No | Yes | No | Yes | Yes |
| `menus.manage` | No | No | No | Yes | Yes |
| `users.manage` | No | No | No | Yes | Yes |
| `users.create` | No | No | No | Yes | Yes |
| `roles.manage` | No | No | No | No | Yes |
| `roles.create` | No | No | No | No | Yes |
| `settings.manage` | No | No | No | Yes | Yes |
| `ads.manage` | No | No | No | Yes | Yes |
| `dashboard.view` | Yes | Yes | Yes | Yes | Yes |
| `themes.manage` | No | No | No | Yes | Yes |
| `api_keys.manage` | No | No | No | No | Yes |

## Workflow Rules

- Author/Reporter and Contributor can submit posts for review with `posts.submit_review`.
- Editor, Admin, and Super Admin can publish directly with `posts.publish`.
- Users without `posts.publish` cannot force published status; published submissions are converted to pending review when they can submit for review.
- Contributor cannot delete own posts because the role does not receive `posts.delete.own`.

## Install / Refresh RBAC

```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan permission:cache-reset
```

The project requires PHP 8.4 or newer based on Composer platform checks.

## Usage Examples

Controller:

```php
$this->authorize('update', $post);

if ($request->user()->can('posts.publish')) {
    // Show or execute direct publishing.
}
```

Blade:

```blade
@can('posts.publish')
    <button type="submit">Publish</button>
@endcan

@can('settings.manage')
    <a href="{{ route('admin.settings.index') }}">Settings</a>
@endcan
```

Middleware:

```php
Route::get('/admin', DashboardController::class)
    ->middleware(['auth', 'permission:dashboard.view']);
```

Custom Blade directives are available:

```blade
@permission('users.manage')
    ...
@endpermission

@role('Super Admin')
    ...
@endrole
```
