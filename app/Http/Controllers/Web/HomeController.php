<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('public.home', [
            'brand' => config('marketing.brand'),
        ]);
    }
}
