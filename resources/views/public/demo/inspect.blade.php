@extends('layouts.public')
@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 lg:px-8">
        <a href="{{ route('demo.transactions.show', $transaction) }}" class="text-sm text-teal-700">← Back</a>
        <h1 class="mt-4 text-2xl font-semibold capitalize text-slate-900">{{ $layer }} payload</h1>
        <pre class="mt-6 overflow-x-auto rounded-xl bg-slate-900 p-6 text-xs text-teal-100">@if ($layer === 'raw'){{ $transaction->raw_payload ?? $transaction->raw_content }}@else{{ json_encode($layer === 'parsed' ? ($transaction->parsed_payload ?? $transaction->parsed_content) : ($transaction->canonical_payload ?? $transaction->canonical_content), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}@endif</pre>
    </div>
@endsection
