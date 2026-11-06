@extends('layouts.app')

@section('title', 'Access denied — Solava')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-sm rounded-lg border border-gray-200 bg-white p-8 text-center shadow-sm">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Solava</h1>
        {{-- task #123: a change refused because the task is deactivated (a
             form submitted from a tab left open, say) says so, instead of
             the generic text. --}}
        @if (isset($exception) && $exception->getMessage() === \App\Support\DeactivatedTask::MESSAGE)
            <p class="mt-4 text-[13px] font-medium text-[#1F2937]">{{ \App\Support\DeactivatedTask::MESSAGE }}</p>
            <p class="mt-1 text-[12px] text-gray-500">Nothing was saved.</p>
        @else
            <p class="mt-4 text-[13px] font-medium text-[#1F2937]">You don't have permission to view this page.</p>
            <p class="mt-1 text-[12px] text-gray-500">If you think this is a mistake, contact your administrator.</p>
        @endif
        <a href="/" class="mt-6 inline-block rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
            Back to Solava
        </a>
    </div>
</div>
@endsection
