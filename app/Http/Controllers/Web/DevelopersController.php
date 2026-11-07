<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class DevelopersController extends Controller
{
    public function __invoke()
    {
        return view('public.developers', [
            'title' => 'Developer Portal',
            'subtitle' => 'API keys, webhooks, sandbox tenants, and OpenAPI surfaces for integrators.',
        ]);
    }
}
