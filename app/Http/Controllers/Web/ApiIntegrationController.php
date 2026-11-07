<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ApiIntegrationController extends Controller
{
    public function __invoke()
    {
        return view('public.api-integration', [
            'title' => 'API Integration',
            'subtitle' => 'REST ingress and webhooks when partners prefer APIs over traditional EDI transport.',
        ]);
    }
}
