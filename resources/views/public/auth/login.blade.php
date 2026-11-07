@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" compact />
    <form method="post" action="{{ route('login.attempt') }}" class="mx-auto max-w-sm space-y-4 px-4 py-12">
        @csrf
        @if ($errors->any())
            <p class="text-sm text-red-600">{{ $errors->first() }}</p>
        @endif
        <input type="email" name="email" placeholder="Email" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        <input type="password" name="password" placeholder="Password" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Remember me</label>
        <button class="w-full rounded-lg bg-teal-600 py-2 text-sm font-semibold text-white">Sign in</button>
        <p class="text-center text-sm text-slate-500"><a href="{{ route('register') }}" class="text-teal-700">Create account</a> · <a href="{{ route('demo.login') }}" class="text-teal-700">Demo login</a></p>
    </form>
@endsection
