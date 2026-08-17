@extends('layouts.app')

@section('title', 'Dashboard — FounderOS')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">Welcome, {{ auth()->user()->name }}</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">Log out</button>
        </form>
    </div>
    <p class="mt-4 text-sm text-gray-500">
        This is a placeholder — the real dashboard (per-company tabs, priority split, kanban) is built in Phase 5.
    </p>
</div>
@endsection
