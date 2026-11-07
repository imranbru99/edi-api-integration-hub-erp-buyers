@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$module->name" :subtitle="$module->description" compact />
    <div class="mx-auto max-w-3xl px-4 py-12 text-sm text-slate-600 lg:px-8">
        Module code: <code class="rounded bg-slate-100 px-2 py-1">{{ $module->code }}</code>
    </div>
@endsection
