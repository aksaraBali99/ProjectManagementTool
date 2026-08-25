@extends('layouts.authenticated')

@section('title', 'Bulk Import — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-[14px] font-medium text-[#1F2937]">Bulk Import</h1>
    <p class="mt-1 text-[11px] text-gray-500">
        Download a live template pre-filled with your current companies and departments, fill it in, then upload it here to review and commit the changes.
    </p>

    <div class="mt-6 rounded-md border border-gray-200 p-4">
        <h2 class="text-[12px] font-medium text-[#1F2937]">1. Download the template</h2>
        <p class="mt-1 text-[11px] text-gray-500">
            The template's Companies and Departments tabs are generated from what's in Solava right now, so any renames you make there are detected as updates instead of duplicates.
        </p>
        <a href="{{ route('import.template') }}"
           class="mt-3 inline-block rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
            Download Template
        </a>
    </div>
</div>
@endsection
