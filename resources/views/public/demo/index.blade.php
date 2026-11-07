@extends('layouts.public')
@section('content')
    <section class="hero-grid border-b border-slate-200 py-20">
        <div class="mx-auto max-w-4xl px-4 text-center lg:px-8 fade-rise">
            <p class="text-sm font-semibold uppercase tracking-widest text-teal-700">Sandbox</p>
            <h1 class="mt-3 font-serif text-4xl font-semibold text-slate-900">Interactive EDI demo</h1>
            <p class="mt-4 text-lg text-slate-600">Sign in as a fictional Global Apparel tenant. Inspect raw X12 and EDIFACT, canonical JSON, and delivery timelines.</p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="{{ route('demo.login') }}" class="rounded-lg bg-teal-600 px-6 py-3 text-sm font-semibold text-white hover:bg-teal-700">Demo sign in</a>
                @auth
                    <a href="{{ route('demo.transactions') }}" class="rounded-lg border border-slate-300 px-6 py-3 text-sm font-semibold">Open transactions</a>
                @endauth
            </div>
            @if ($demoEnabled)
                <p class="mt-6 text-xs text-slate-500">APP_DEMO_MODE is on — operators can reset demo data from the transactions screen.</p>
            @endif
        </div>
    </section>
    <section class="mx-auto max-w-4xl px-4 py-16 text-sm text-slate-600 lg:px-8">
        <h2 class="text-lg font-semibold text-slate-900">Demo credentials</h2>
        <p class="mt-2">Use <code>DEMO_PASSWORD</code> from <code>.env</code> with any seeded address such as <code>{{ config('edi-hub.demo.emails.edi') }}</code>.</p>
    </section>
@endsection
