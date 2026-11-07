@extends('layouts.public')
@section('content')
    <x-public.page-hero title="Demo sign in" subtitle="Fictional tenant: Global Apparel Manufacturing Ltd" compact />
    <form method="post" action="{{ route('demo.login.attempt') }}" class="mx-auto max-w-sm space-y-4 px-4 py-12">
        @csrf
        @if ($errors->any())
            <p class="text-sm text-red-600">{{ $errors->first() }}</p>
        @endif
        <input type="email" name="email" value="{{ old('email', config('edi-hub.demo.emails.edi')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        <input type="password" name="password" placeholder="DEMO_PASSWORD" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        <button class="w-full rounded-lg bg-teal-600 py-2 text-sm font-semibold text-white">Enter demo</button>
    </form>
@endsection
