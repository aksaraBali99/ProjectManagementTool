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
           class="mt-3 inline-block rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
            Download Template
        </a>
    </div>

    <div class="mt-4 rounded-md border border-gray-200 p-4">
        <h2 class="text-[12px] font-medium text-[#1F2937]">2. Upload your completed file</h2>
        <p class="mt-1 text-[11px] text-gray-500">
            Every row is validated before anything is written — you'll get a chance to review the results and fix any errors before committing.
        </p>

        <form method="POST" action="{{ route('import.upload') }}" enctype="multipart/form-data" class="mt-3">
            @csrf
            <input type="file" name="file" accept=".xlsx" required
                class="block w-full text-[12px] text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-[12px] file:font-medium file:text-gray-700 hover:file:bg-gray-200">
            @error('file')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="mt-3 rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                Upload & Validate
            </button>
        </form>
    </div>
</div>
@endsection
