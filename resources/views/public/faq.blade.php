@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" />
    <div class="mx-auto max-w-3xl divide-y divide-slate-200 px-4 py-12 lg:px-8" x-data="{ open: null }">
        @foreach ($items as $i => $item)
            <div class="py-4">
                <button type="button" class="flex w-full items-center justify-between text-left text-base font-semibold text-slate-900" @click="open = open === {{ $i }} ? null : {{ $i }}">
                    {{ $item['q'] }}
                    <span class="text-teal-600" x-text="open === {{ $i }} ? '−' : '+'"></span>
                </button>
                <p class="mt-3 text-sm leading-relaxed text-slate-600" x-show="open === {{ $i }}" x-transition>{{ $item['a'] }}</p>
            </div>
        @endforeach
    </div>
    <x-public.cta />
@endsection
