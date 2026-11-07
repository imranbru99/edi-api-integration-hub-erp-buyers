@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" subtitle="X12 and EDIFACT document types supported in the canonical model." />
    <div class="mx-auto max-w-5xl space-y-12 px-4 py-16 lg:px-8">
        @foreach ($standards as $key => $family)
            <div>
                <h2 class="font-serif text-2xl font-semibold text-slate-900">{{ $family['name'] }}</h2>
                <p class="mt-2 text-slate-600">{{ $family['summary'] }}</p>
                <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                    @foreach ($family['types'] as $code => $type)
                        <li>
                            <a href="{{ route('edi-standards.show', strtolower($code)) }}" class="block rounded-xl border border-slate-200 bg-white px-4 py-4 hover:border-teal-500">
                                <span class="font-mono text-sm text-teal-700">{{ $code }}</span>
                                <span class="mt-1 block font-semibold text-slate-900">{{ $type['name'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
@endsection
