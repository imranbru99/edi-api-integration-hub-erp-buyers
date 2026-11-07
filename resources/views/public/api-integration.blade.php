@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" :subtitle="$subtitle ?? null" />
    <div class="mx-auto max-w-3xl px-4 py-16 lg:px-8">
        <div class="prose prose-slate max-w-none">
            @include('public.partials.' . 'api-integration')
        </div>
    </div>
    <x-public.cta />
@endsection
