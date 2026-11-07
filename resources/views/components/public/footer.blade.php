<footer class="mt-24 border-t border-slate-200 bg-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 lg:grid-cols-4 lg:px-8">
        <div class="lg:col-span-2">
            <p class="text-lg font-semibold text-slate-900">{{ config('marketing.brand.name') }}</p>
            <p class="mt-3 max-w-md text-sm leading-relaxed text-slate-600">{{ config('marketing.brand.tagline') }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Product</p>
            <ul class="mt-4 space-y-2 text-sm text-slate-600">
                <li><a href="{{ route('modules') }}" class="hover:text-teal-700">Modules</a></li>
                <li><a href="{{ route('security') }}" class="hover:text-teal-700">Security</a></li>
                <li><a href="{{ route('developers') }}" class="hover:text-teal-700">Developers</a></li>
            </ul>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Company</p>
            <ul class="mt-4 space-y-2 text-sm text-slate-600">
                <li><a href="{{ route('about') }}" class="hover:text-teal-700">About</a></li>
                <li><a href="{{ route('blog') }}" class="hover:text-teal-700">Blog</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-teal-700">Contact</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-teal-700">FAQ</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-slate-100 py-6 text-center text-xs text-slate-500">
        © {{ date('Y') }} {{ config('marketing.brand.name') }}. Fictional demo data where indicated.
    </div>
</footer>
