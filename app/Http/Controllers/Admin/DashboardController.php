<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user->can('dashboard.view'), 403);
        
        // Cache dashboard stats for 5 minutes
        $stats = Cache::remember('admin.dashboard.stats', 300, function () use ($user) {
            $query = Post::query();
            
            if (! $user->can('posts.edit.any')) {
                $query->where('user_id', $user->id);
            }
            
            return [
                'total_posts' => $query->count(),
                'published_posts' => (clone $query)->where('status', 'published')->count(),
                'pending_posts' => (clone $query)->where('status', 'pending')->count(),
                'draft_posts' => (clone $query)->where('status', 'draft')->count(),
                'total_users' => $user->can('users.manage') ? User::count() : 1,
                'total_categories' => Category::count(),
                'total_media' => Media::count(),
                'total_media_size' => Media::sum('file_size') ?? 0,
            ];
        });

        $recentPosts = Post::with('author', 'categories')
            ->when(! $user->can('posts.edit.any'), function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })
            ->latest()
            ->take(8)
            ->get();

        $popularPosts = Post::with('author')
            ->where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        $userActivity = User::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentPosts',
            'popularPosts',
            'userActivity'
        ));
    }
}
