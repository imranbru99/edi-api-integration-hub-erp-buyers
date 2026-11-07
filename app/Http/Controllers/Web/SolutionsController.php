<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SolutionsController extends Controller
{
    public function show(string $industry): View
    {
        $solutions = config('marketing.solutions');
        if (! isset($solutions[$industry])) {
            abort(404);
        }

        return view('public.solutions.show', [
            'industry' => $industry,
            'solution' => $solutions[$industry],
        ]);
    }
}
