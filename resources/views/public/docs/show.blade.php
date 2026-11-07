@extends('layouts.public')
@section('content')
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 lg:grid-cols-4 lg:px-8">
        <aside class="lg:col-span-1 text-sm">
            <p class="font-semibold text-slate-900">Documentation</p>
            <ul class="mt-4 space-y-2 text-slate-600">
                @foreach ($nav as $key => $label)
                    <li>
                        <a href="{{ route('docs.show', strtolower($key)) }}" @class(['text-teal-700 font-medium' => $key === $slug])>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </aside>
        <article class="lg:col-span-3 prose prose-slate max-w-none prose-pre:bg-slate-900">
            {!! $html !!}
        </article>
    </div>
@endsection
