<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DocsController extends Controller
{
    public function index(): View
    {
        return view('public.docs.index', [
            'title' => 'Documentation',
            'nav' => config('marketing.docs_nav'),
        ]);
    }

    public function show(string $page): View
    {
        $nav = config('marketing.docs_nav');
        $key = strtoupper($page);

        if (! isset($nav[$key])) {
            abort(404);
        }

        $path = base_path('docs/'.$key.'.md');
        if (! File::exists($path)) {
            abort(404);
        }

        $markdown = File::get($path);
        $html = Str::markdown($markdown);

        return view('public.docs.show', [
            'title' => $nav[$key],
            'slug' => $key,
            'nav' => $nav,
            'html' => $html,
        ]);
    }
}
