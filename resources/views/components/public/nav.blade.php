@php($brand = config('marketing.brand.name'))
<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 lg:px-8">
        <a href="{{ route('home') }}" class="group flex items-center gap-2">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-teal-600 text-sm font-bold text-white shadow-sm transition group-hover:bg-teal-700">EDI</span>
            <span class="text-lg font-semibold tracking-tight text-slate-900">{{ $brand }}</span>
        </a>

        <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 lg:flex">
            <a href="{{ route('features') }}" class="hover:text-teal-700">Features</a>
            <a href="{{ route('edi-gateway') }}" class="hover:text-teal-700">EDI Gateway</a>
            <a href="{{ route('erp-integrations') }}" class="hover:text-teal-700">ERP</a>
            <a href="{{ route('pricing') }}" class="hover:text-teal-700">Pricing</a>
            <a href="{{ route('docs') }}" class="hover:text-teal-700">Docs</a>
            <a href="{{ route('demo.index') }}" class="hover:text-teal-700">Demo</a>
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-teal-700">Sign in</a>
            <a href="{{ route('contact') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-700">Talk to sales</a>
        </div>

        <button type="button" class="lg:hidden rounded-lg border border-slate-200 p-2" @click="navOpen = !navOpen" aria-label="Menu">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
    <div class="border-t border-slate-200 bg-white px-4 py-4 lg:hidden" x-show="navOpen" x-transition>
        <div class="flex flex-col gap-3 text-sm font-medium">
            <a href="{{ route('features') }}">Features</a>
            <a href="{{ route('edi-gateway') }}">EDI Gateway</a>
            <a href="{{ route('erp-integrations') }}">ERP Integrations</a>
            <a href="{{ route('pricing') }}">Pricing</a>
            <a href="{{ route('docs') }}">Documentation</a>
            <a href="{{ route('demo.index') }}">Interactive Demo</a>
            <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</header>
