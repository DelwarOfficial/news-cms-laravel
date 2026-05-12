<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\{Post, User, Category, Comment, ContentPlacement, Media, Widget, Advertisement, Tag, Setting};
use App\Observers\CategoryObserver;
use App\Observers\ContentPlacementObserver;
use App\Observers\AdvertisementObserver;
use App\Observers\PostObserver;
use App\Policies\{PostPolicy, UserPolicy, CategoryPolicy, CommentPolicy, MediaPolicy, WidgetPolicy, AdvertisementPolicy, TagPolicy, SettingPolicy};
use App\Services\TickerHeadlineService;
use App\Support\CategoryRepository;

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

        Post::observe(PostObserver::class);
        Category::observe(CategoryObserver::class);
        ContentPlacement::observe(ContentPlacementObserver::class);
        Advertisement::observe(AdvertisementObserver::class);

        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Blade::if('permission', fn (string $permission) => auth()->check() && auth()->user()->can($permission));
        Blade::if('role', fn (string|array $roles) => auth()->check() && auth()->user()->hasRole($roles));

        View::composer('layouts.app', function ($view) {
            $view->with('tickerHeadlines', app(TickerHeadlineService::class)->get());
            $view->with('siteCategories', Cache::remember(
                'layout:site-categories:v2',
                now()->addSeconds((int) config('homepage.cache.ttl', 300)),
                fn () => CategoryRepository::parents(),
            ));
        });
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
