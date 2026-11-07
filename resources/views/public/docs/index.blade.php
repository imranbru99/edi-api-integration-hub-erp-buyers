@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" subtitle="Technical reference for operators and integrators." />
    <div class="mx-auto max-w-3xl px-4 py-16 lg:px-8">
        <ul class="grid gap-2 sm:grid-cols-2">
            @foreach ($nav as $slug => $label)
                <li>
                    <a href="{{ route('docs.show', strtolower($slug)) }}" class="block rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-medium hover:border-teal-500">{{ $label }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
