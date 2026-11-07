@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" />
    <div class="mx-auto grid max-w-5xl gap-6 px-4 py-16 sm:grid-cols-2 lg:px-8">
        @foreach ($modules as $module)
            <a href="{{ route('modules.show', $module->code) }}" class="rounded-2xl border border-slate-200 bg-white p-6 hover:border-teal-500">
                <h2 class="text-lg font-semibold text-slate-900">{{ $module->name }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $module->description }}</p>
            </a>
        @endforeach
    </div>
@endsection
