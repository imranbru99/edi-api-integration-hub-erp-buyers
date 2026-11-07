<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class FeaturesController extends Controller
{
    public function __invoke()
    {
        return view('public.features', [
            'title' => 'Platform Features',
            'subtitle' => 'Everything required to operate B2B EDI alongside your ERP—not bolted on as an afterthought.',
        ]);
    }
}
