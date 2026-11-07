<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class SecurityController extends Controller
{
    public function __invoke()
    {
        return view('public.security', [
            'title' => 'Security & Compliance',
            'subtitle' => 'Tenant isolation, encrypted credentials, retention policies, and audit-ready exports.',
        ]);
    }
}
