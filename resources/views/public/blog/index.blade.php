@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" />
    <div class="mx-auto max-w-3xl divide-y divide-slate-200 px-4 py-12 lg:px-8">
        @forelse ($posts as $post)
            <article class="py-8">
                <a href="{{ route('blog.show', $post->slug) }}" class="font-serif text-2xl font-semibold text-slate-900 hover:text-teal-700">{{ $post->title }}</a>
                <p class="mt-2 text-sm text-slate-500">{{ optional($post->published_at)->format('M j, Y') }}</p>
                <p class="mt-3 text-slate-600">{{ $post->excerpt }}</p>
            </article>
        @empty
            <p class="py-8 text-slate-600">Run <code>php artisan edi-hub:demo-setup</code> to publish sample posts.</p>
        @endforelse
        {{ $posts->links() }}
    </div>
@endsection
