@extends('layouts.public')

@section('content')
    <section class="hero-grid relative min-h-[88vh] flex flex-col justify-center overflow-hidden border-b border-slate-200/80">
        <div class="pointer-events-none absolute -right-24 top-20 h-96 w-96 rounded-full bg-teal-400/10 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -left-32 bottom-0 h-80 w-80 rounded-full bg-slate-400/10 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-20 lg:grid-cols-12 lg:items-center lg:px-8 lg:py-28">
            <div class="lg:col-span-6 fade-rise">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-700">{{ $brand['name'] }}</p>
                <h1 class="mt-4 font-serif text-4xl font-semibold leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                    EDI that your ERP team can operate—not outsource.
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-600">
                    Normalize X12 and EDIFACT into canonical documents, route to trading partners, and sync with ERP connectors— with lineage your auditors can trust.
                </p>
                <div class="mt-10 flex flex-wrap gap-4 fade-rise-delay">
                    <a href="{{ route('demo.index') }}" class="rounded-lg bg-teal-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-teal-600/20 hover:bg-teal-700">Launch interactive demo</a>
                    <a href="{{ route('docs') }}" class="rounded-lg border border-slate-300 bg-white/80 px-6 py-3 text-sm font-semibold text-slate-800 hover:border-teal-500 hover:text-teal-800">Read architecture docs</a>
                </div>
            </div>

            <div class="lg:col-span-6" x-data="{ tab: 'flow' }">
                <div class="relative rounded-2xl border border-slate-200/80 bg-white/70 p-1 shadow-2xl shadow-slate-900/5 backdrop-blur">
                    <div class="flex gap-1 rounded-xl bg-slate-100 p-1 text-xs font-medium">
                        <button type="button" @click="tab='flow'" :class="tab==='flow' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="flex-1 rounded-lg px-3 py-2">Document flow</button>
                        <button type="button" @click="tab='partners'" :class="tab==='partners' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="flex-1 rounded-lg px-3 py-2">Partner grid</button>
                    </div>
                    <div class="p-6">
                        <div x-show="tab==='flow'" x-transition.opacity class="space-y-4 font-mono text-xs text-slate-700">
                            <div class="rounded-lg bg-slate-900 p-4 text-teal-300">850 PO → validate → canonical → ERP mock</div>
                            <div class="flex items-center gap-2 text-slate-500">
                                <span class="h-2 w-2 rounded-full bg-teal-500 animate-pulse"></span> 997 FA returned · latency 240ms
                            </div>
                            <div class="rounded-lg border border-dashed border-slate-300 p-4">ORDERS (EDIFACT) · EU buyer profile · mapped</div>
                        </div>
                        <div x-show="tab==='partners'" x-transition.opacity class="grid grid-cols-2 gap-3 text-sm">
                            @foreach (['Global Retail Buyer', 'European Fashion Group', 'Asia Textile Supplier', 'Intl Logistics Partner'] as $partner)
                                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-4">
                                    <p class="font-semibold text-slate-900">{{ $partner }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Fictional · demo only</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-20 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-3">
            @foreach ([
                ['title' => 'Canonical first', 'body' => 'Parse once into purchase orders, ASNs, and invoices your ERP understands.'],
                ['title' => 'Partner-aware routing', 'body' => 'ISA/GS and EDIFACT interchange profiles per buyer—with mock and planned ERP adapters labeled clearly.'],
                ['title' => 'Operate with evidence', 'body' => 'Timeline every validation, map, and delivery. Reset the demo tenant when APP_DEMO_MODE is on.'],
            ] as $pillar)
                <div class="border-l-2 border-teal-500 pl-6">
                    <h2 class="text-lg font-semibold text-slate-900">{{ $pillar['title'] }}</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $pillar['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <x-public.cta />
@endsection
