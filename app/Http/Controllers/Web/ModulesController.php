<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\View\View;

class ModulesController extends Controller
{
    public function index(): View
    {
        $modules = Module::query()->where('is_available', true)->orderBy('sort_order')->get();

        if ($modules->isEmpty()) {
            $modules = collect(config('marketing.modules_fallback'))->map(fn ($m, $code) => (object) array_merge($m, ['code' => $code]));
        }

        return view('public.modules.index', [
            'title' => 'Platform Modules',
            'modules' => $modules,
        ]);
    }

    public function show(string $code): View
    {
        $module = Module::query()->where('code', $code)->first();

        if (! $module) {
            $fallback = config('marketing.modules_fallback.'.$code);
            if (! $fallback) {
                abort(404);
            }
            $module = (object) array_merge($fallback, ['code' => $code]);
        }

        return view('public.modules.show', ['module' => $module]);
    }
}
