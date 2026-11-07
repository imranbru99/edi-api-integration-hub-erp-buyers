@extends('layouts.public')
@section('content')
    <div class="mx-auto max-w-5xl px-4 py-10 lg:px-8">
        <a href="{{ route('demo.transactions') }}" class="text-sm text-teal-700">← Transactions</a>
        <h1 class="mt-4 font-serif text-3xl font-semibold text-slate-900">{{ $transaction->document_type }} · {{ $transaction->control_number }}</h1>
        <p class="mt-2 text-sm text-slate-500">{{ $transaction->tradingPartner?->name }} · {{ strtoupper($transaction->standard) }}</p>

        <div class="mt-8 flex flex-wrap gap-3">
            @foreach (['raw', 'parsed', 'canonical'] as $layer)
                <a href="{{ route('demo.transactions.inspect', [$transaction, $layer]) }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium hover:border-teal-500">Inspect {{ $layer }}</a>
            @endforeach
            <a href="{{ route('demo.transactions.timeline', $transaction) }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium hover:border-teal-500">Timeline</a>
        </div>

        <form method="post" action="{{ route('demo.transactions.simulate-delivery', $transaction) }}" class="mt-8">
            @csrf
            <button class="rounded-lg bg-teal-600 px-5 py-2 text-sm font-semibold text-white hover:bg-teal-700">Simulate ERP delivery</button>
        </form>

        @if ($transaction->purchaseOrder)
            <div class="mt-10 rounded-xl border border-slate-200 bg-slate-50 p-6">
                <h2 class="font-semibold text-slate-900">Linked purchase order</h2>
                <p class="mt-2 font-mono text-sm">{{ $transaction->purchaseOrder->po_number }} · {{ $transaction->purchaseOrder->status }}</p>
            </div>
        @endif
    </div>
@endsection
