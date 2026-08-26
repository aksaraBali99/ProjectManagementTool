@extends('layouts.authenticated')

@section('title', 'Add document — Solava')

@section('content')
<div class="mx-auto max-w-xl">
    <a href="{{ route('documents.index', $organization) }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Documents</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Add document</h1>
    <p class="mt-1 text-[11px] text-gray-500">Adding to <span class="font-medium text-[#1F2937]">{{ $organization->name }}</span>.</p>

    <form method="POST" action="{{ route('documents.store') }}" class="mt-6 space-y-4" novalidate>
        @csrf
        <input type="hidden" name="organization_id" value="{{ $organization->id }}">
        <input type="hidden" name="from_documents_page" value="1">

        <div>
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name <span class="text-red-600">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            @error('name')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="link" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Document link <span class="text-red-600">*</span></label>
            <input id="link" name="link" type="url" value="{{ old('link') }}" required
                placeholder="https://…"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            @error('link')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="access_level" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Access level <span class="text-red-600">*</span></label>
            <select id="access_level" name="access_level" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                @foreach (\App\Enums\DocumentAccessLevel::cases() as $level)
                    <option value="{{ $level->value }}" {{ old('access_level') === $level->value ? 'selected' : '' }}>{{ $level->label() }}</option>
                @endforeach
            </select>
            @error('access_level')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                Add document
            </button>
            <a href="{{ route('documents.index', $organization) }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>
@endsection
