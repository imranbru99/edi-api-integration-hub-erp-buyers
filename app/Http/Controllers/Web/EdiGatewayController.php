<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class EdiGatewayController extends Controller
{
    public function __invoke()
    {
        return view('public.edi-gateway', [
            'title' => 'EDI Gateway',
            'subtitle' => 'Ingestion, validation, partner routing, and delivery—with full payload lineage.',
        ]);
    }
}
