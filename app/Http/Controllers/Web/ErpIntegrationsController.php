<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ErpIntegrationsController extends Controller
{
    public function index(): View
    {
        return view('public.erp-integrations.index', [
            'title' => 'ERP Integrations',
            'integrations' => config('marketing.erp_integrations'),
        ]);
    }

    public function show(string $slug): View
    {
        $integrations = config('marketing.erp_integrations');
        if (! isset($integrations[$slug])) {
            abort(404);
        }

        return view('public.erp-integrations.show', [
            'slug' => $slug,
            'integration' => $integrations[$slug],
        ]);
    }
}
