@extends('layouts.public')
@section('content')
    <x-public.page-hero :title="$title" compact />
    <form method="post" action="{{ route('register.attempt') }}" class="mx-auto max-w-sm space-y-4 px-4 py-12">
        @csrf
        <input name="name" placeholder="Name" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        <input type="email" name="email" placeholder="Email" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        <input type="password" name="password" placeholder="Password" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        <input type="password" name="password_confirmation" placeholder="Confirm" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        <button class="w-full rounded-lg bg-teal-600 py-2 text-sm font-semibold text-white">Register</button>
    </form>
@endsection
