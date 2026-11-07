@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" />
    <form method="post" action="{{ route('contact.store') }}" class="mx-auto max-w-xl space-y-4 px-4 py-12 lg:px-8">
        @csrf
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif
        <div>
            <label class="block text-sm font-medium text-slate-700">Name</label>
            <input name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Subject</label>
            <input name="subject" value="{{ old('subject') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Message</label>
            <textarea name="message" rows="5" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('message') }}</textarea>
        </div>
        <button type="submit" class="rounded-lg bg-teal-600 px-6 py-3 text-sm font-semibold text-white hover:bg-teal-700">Send message</button>
    </form>
@endsection
