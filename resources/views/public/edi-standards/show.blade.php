@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$type['name'] . ' (' . $code . ')'" :subtitle="$type['summary']" compact />
    <div class="mx-auto max-w-3xl px-4 py-12 lg:px-8 prose prose-slate">
        <p>Family: <strong>{{ $meta['name'] }}</strong> · Typical direction: {{ $type['direction'] }}</p>
        <p><a href="{{ route('edi-standards') }}">← All standards</a></p>
    </div>
@endsection
