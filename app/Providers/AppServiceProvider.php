<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use App\Models\{Post, User, Category, Comment, Media, Widget, Advertisement, Tag, Setting};
use App\Policies\{PostPolicy, UserPolicy, CategoryPolicy, CommentPolicy, MediaPolicy, WidgetPolicy, AdvertisementPolicy, TagPolicy, SettingPolicy};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enable Bootstrap pagination
        Paginator::useBootstrapFive();

        // Register all authorization policies
        $this->registerPolicies();



        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Blade::if('permission', fn (string $permission) => auth()->check() && auth()->user()->can($permission));
        Blade::if('role', fn (string|array $roles) => auth()->check() && auth()->user()->hasRole($roles));
    }

    /**
     * Register all authorization policies
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(Widget::class, WidgetPolicy::class);
        Gate::policy(Advertisement::class, AdvertisementPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
    }
}
