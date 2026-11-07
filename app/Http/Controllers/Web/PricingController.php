<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __invoke(): View
    {
        $plans = Plan::query()->where('is_active', true)->orderBy('sort_order')->get();

        if ($plans->isEmpty()) {
            $plans = collect(config('marketing.pricing_fallback'));
        }

        return view('public.pricing', [
            'title' => 'Pricing',
            'plans' => $plans,
        ]);
    }
}
