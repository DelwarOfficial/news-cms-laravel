<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'pending_posts' => Post::where('status', 'pending')->count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
            'total_users' => User::count(),
            'total_categories' => Category::count(),
            'total_comments' => \App\Models\Comment::count(),
        ];

        $recentPosts = Post::with('author')
            ->latest()
            ->take(8)
            ->get();

        $popularPosts = Post::with('author')
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPosts', 'popularPosts'));
    }
}