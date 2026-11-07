@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$solution['title']" :subtitle="$solution['headline']" />
    <div class="mx-auto max-w-3xl px-4 py-12 prose prose-slate lg:px-8">
        <p>EDI Integration Hub routes buyer-specific X12 and EDIFACT into your ERP while preserving audit trails for {{ str_replace('-', ' ', $industry) }} operations.</p>
        <p><a href="{{ route('solutions.show', 'garments') }}">Garments</a> · <a href="{{ route('contact') }}">Talk to sales</a></p>
    </div>
    <x-public.cta :headline="'See the ' . $solution['title'] . ' workflow in the demo'" />
@endsection
