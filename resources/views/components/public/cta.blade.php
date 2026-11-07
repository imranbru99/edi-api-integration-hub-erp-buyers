@props(['headline' => 'Ready to unify EDI and ERP?', 'primaryUrl' => null, 'primaryLabel' => 'Book a walkthrough'])
<section class="relative overflow-hidden bg-slate-900 py-16 text-white">
    <div class="absolute inset-0 opacity-40" style="background: radial-gradient(circle at 30% 50%, rgb(20 184 166 / 0.35), transparent 55%);"></div>
    <div class="relative mx-auto max-w-4xl px-4 text-center lg:px-8">
        <h2 class="font-serif text-3xl font-semibold tracking-tight">{{ $headline }}</h2>
        <p class="mt-4 text-slate-300">Launch a sandbox tenant or explore the interactive demo with realistic X12 and EDIFACT traffic.</p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ $primaryUrl ?? route('contact') }}" class="rounded-lg bg-teal-500 px-6 py-3 text-sm font-semibold text-slate-900 hover:bg-teal-400">{{ $primaryLabel }}</a>
            <a href="{{ route('demo.index') }}" class="rounded-lg border border-slate-600 px-6 py-3 text-sm font-semibold text-white hover:border-teal-400">Open demo</a>
        </div>
    </div>
</section>
