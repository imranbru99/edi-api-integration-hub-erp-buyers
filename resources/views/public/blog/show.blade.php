@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$post->title" :subtitle="$post->excerpt" compact />
    <div class="mx-auto max-w-3xl px-4 py-12 prose prose-slate lg:px-8">
        {!! \Illuminate\Support\Str::markdown($post->body) !!}
    </div>
@endsection
