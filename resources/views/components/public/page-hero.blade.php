@props(['title', 'subtitle' => null, 'compact' => false])
<section class="hero-grid border-b border-slate-200/80 {{ $compact ? 'py-12' : 'py-16 lg:py-20' }}">
    <div class="mx-auto max-w-4xl px-4 text-center lg:px-8 fade-rise">
        <h1 class="font-serif text-4xl font-semibold tracking-tight text-slate-900 lg:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-slate-600 fade-rise-delay">{{ $subtitle }}</p>
        @endif
        {{ $slot ?? '' }}
    </div>
</section>
