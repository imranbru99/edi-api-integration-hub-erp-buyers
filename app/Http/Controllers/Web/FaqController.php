<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function __invoke()
    {
        return view('public.faq', [
            'title' => 'Frequently Asked Questions',
            'items' => config('marketing.faq'),
        ]);
    }
}
