<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class EdiStandardsController extends Controller
{
    public function index(): View
    {
        return view('public.edi-standards.index', [
            'title' => 'EDI Standards',
            'standards' => config('marketing.edi_standards'),
        ]);
    }

    public function show(string $code): View
    {
        $code = strtoupper($code);
        $all = config('marketing.edi_standards');

        foreach ($all as $family => $meta) {
            if (isset($meta['types'][$code])) {
                return view('public.edi-standards.show', [
                    'family' => $family,
                    'code' => $code,
                    'meta' => $meta,
                    'type' => $meta['types'][$code],
                ]);
            }
        }

        abort(404);
    }
}
