@extends('layouts.app')

@section('title', 'Something went wrong — Solava')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-sm rounded-[12px] border border-gray-200 bg-white p-8 text-center shadow-sm">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Solava</h1>
        <p class="mt-4 text-[13px] font-medium text-[#1F2937]">Something went wrong on our end.</p>
        <p class="mt-1 text-[12px] text-gray-500">Please try again, or contact your administrator if it keeps happening.</p>
        <a href="/" class="mt-6 inline-block rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
            Back to Solava
        </a>
    </div>
</div>
@endsection
