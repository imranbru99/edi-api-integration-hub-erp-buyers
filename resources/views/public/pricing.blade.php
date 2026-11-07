@extends('layouts.public')
@section('content')
    <x-public.page-hero title="Pricing" subtitle="Transparent tiers for manufacturers scaling EDI volume." />
    <div class="mx-auto grid max-w-6xl gap-6 px-4 py-16 lg:grid-cols-3 lg:px-8">
        @foreach ($plans as $plan)
            @php
                $isObject = is_object($plan);
                $name = $isObject ? $plan->name : $plan['name'];
                $desc = $isObject ? $plan->description : ($plan['description'] ?? '');
                $monthly = $isObject ? $plan->price_monthly : ($plan['price_monthly'] ?? null);
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-900">{{ $name }}</h2>
                <p class="mt-4 text-3xl font-semibold text-teal-700">
                    @if ($monthly)
                        ${{ number_format((float) $monthly, 0) }}<span class="text-base font-normal text-slate-500">/mo</span>
                    @else
                        Custom
                    @endif
                </p>
                <p class="mt-4 text-sm text-slate-600">{{ $desc }}</p>
                <a href="{{ route('contact') }}" class="mt-8 inline-block text-sm font-semibold text-teal-700 hover:text-teal-800">Contact sales →</a>
            </div>
        @endforeach
    </div>
    <x-public.cta />
@endsection
