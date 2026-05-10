<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function show($username)
    {
        $author = User::where('username', $username)->firstOrFail();
        
        $posts = $author->posts()
            ->published()
            ->latest()
            ->paginate(12);

        return view('front.author', compact('author', 'posts'));
    }
}
