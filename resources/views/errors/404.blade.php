@extends('layouts.app')

@section('title', 'Page not found — Solava')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-sm rounded-lg border border-gray-200 bg-white p-8 text-center shadow-sm">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Solava</h1>
        <p class="mt-4 text-[13px] font-medium text-[#1F2937]">We couldn't find the page you're looking for.</p>
        <p class="mt-1 text-[12px] text-gray-500">It may have been moved, or the link may be out of date.</p>
        <a href="/" class="mt-6 inline-block rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
            Back to Solava
        </a>
    </div>
</div>
@endsection
