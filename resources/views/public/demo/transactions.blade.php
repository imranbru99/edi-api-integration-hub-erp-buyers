@extends('layouts.public')
@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-6 lg:px-8">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">EDI transactions</h1>
                <p class="text-sm text-slate-500">{{ $tenant->name }} <span class="text-amber-700">(fictional demo org)</span></p>
            </div>
            @if (config('edi-hub.demo.enabled'))
                <form method="post" action="{{ route('demo.reset') }}" onsubmit="return confirm('Reset all demo EDI data?')">
                    @csrf
                    <button class="text-sm font-medium text-red-700 hover:underline">Reset demo data</button>
                </form>
            @endif
        </div>
    </div>
    <div class="mx-auto max-w-6xl px-4 py-10 lg:px-8">
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Partner</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($transactions as $txn)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $txn->control_number }}</td>
                            <td class="px-4 py-3">{{ $txn->document_type }}</td>
                            <td class="px-4 py-3">{{ $txn->tradingPartner?->name }}</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs">{{ $txn->status }}</span></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('demo.transactions.show', $txn) }}" class="font-medium text-teal-700">Open</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $transactions->links() }}</div>
    </div>
@endsection
