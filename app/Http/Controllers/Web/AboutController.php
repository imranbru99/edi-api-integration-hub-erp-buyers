<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    public function __invoke()
    {
        return view('public.about', [
            'title' => 'About EDI Integration Hub',
            'subtitle' => 'Built for apparel supply chains connecting legacy ERPs to modern buyer networks.',
        ]);
    }
}
