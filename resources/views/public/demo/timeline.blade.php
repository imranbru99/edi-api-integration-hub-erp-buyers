@extends('layouts.public')
@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 lg:px-8">
        <a href="{{ route('demo.transactions.show', $transaction) }}" class="text-sm text-teal-700">← Back</a>
        <h1 class="mt-4 text-2xl font-semibold text-slate-900">Event timeline</h1>
        <ol class="relative mt-10 border-l border-slate-200">
            @foreach ($transaction->events as $event)
                <li class="mb-8 ml-6">
                    <span class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full bg-teal-500"></span>
                    <p class="text-sm font-semibold text-slate-900">{{ $event->event }}</p>
                    <p class="text-xs text-slate-500">{{ $event->created_at }}</p>
                    <p class="mt-1 text-sm text-slate-600">{{ $event->message }}</p>
                </li>
            @endforeach
        </ol>
    </div>
@endsection
