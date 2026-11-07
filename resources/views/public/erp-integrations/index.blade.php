@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" subtitle="Mock adapters validate architecture; planned connectors ship per engagement." />
    <div class="mx-auto max-w-5xl px-4 py-16 lg:px-8">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-slate-500">
                <tr>
                    <th class="py-3">System</th>
                    <th class="py-3">Category</th>
                    <th class="py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($integrations as $slug => $row)
                    <tr>
                        <td class="py-4 font-medium"><a href="{{ route('erp-integrations.show', $slug) }}" class="text-teal-700 hover:underline">{{ $row['name'] }}</a></td>
                        <td class="py-4 text-slate-600">{{ $row['category'] }}</td>
                        <td class="py-4">
                            @if ($row['status'] === 'mock')
                                <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-800">Mock (demo)</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Planned</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
