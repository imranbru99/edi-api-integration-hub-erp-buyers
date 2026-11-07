<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::withoutGlobalScopes()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('public.blog.index', [
            'title' => 'Blog',
            'posts' => $posts,
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::withoutGlobalScopes()
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return view('public.blog.show', ['post' => $post]);
    }
}
