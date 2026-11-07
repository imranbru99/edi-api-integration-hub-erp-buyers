<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('marketing.brand.name') }} — {{ config('marketing.brand.name') }}</title>
    <meta name="description" content="{{ $metaDescription ?? config('marketing.brand.tagline') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=IBM+Plex+Serif:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 text-slate-800 antialiased font-sans" x-data="{ navOpen: false }">
    @if (session('demo_mode') || request()->is('demo*'))
        <x-public.demo-banner />
    @endif

    <x-public.nav />

    <main>
        @if (session('success'))
            <div class="bg-teal-600 text-white text-center text-sm py-2 px-4">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>

    <x-public.footer />

    @stack('scripts')
</body>
</html>
