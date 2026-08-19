@extends('layouts.authenticated')

@section('title', 'Dashboard — Solva')

@section('content')
<div>
    <h1 class="text-xl font-semibold text-gray-900">Welcome, {{ auth()->user()->name }}</h1>
    <p class="mt-4 text-sm text-gray-500">
        This is a placeholder — the real dashboard (per-company tabs, priority split, kanban) is built in Phase 5.
    </p>
</div>
@endsection
