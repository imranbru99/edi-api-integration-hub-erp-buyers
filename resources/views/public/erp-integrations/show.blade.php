@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$integration['name']" compact />
    <div class="mx-auto max-w-3xl px-4 py-12 lg:px-8 prose prose-slate">
        <p>Status:
            @if ($integration['status'] === 'mock')
                <strong class="text-teal-700">Mock adapter</strong> — safe for sandbox ERP sync in demo tenants.
            @else
                <strong>Planned</strong> — contact sales for delivery timeline.
            @endif
        </p>
        <p>Category: {{ $integration['category'] }}</p>
    </div>
@endsection
