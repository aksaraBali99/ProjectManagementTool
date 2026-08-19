@extends('layouts.authenticated')

@section('title', 'Add company — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('organizations.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Companies</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Add company</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-[8px] bg-red-50 p-3 text-[12px] text-red-700">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('organizations.store') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="slug" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug') }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            <p class="mt-1 text-[10px] text-gray-500">Letters, numbers, dashes, and underscores only.</p>
        </div>

        <div>
            <label for="accent_color" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Accent color</label>
            <input id="accent_color" name="accent_color" type="color" value="{{ old('accent_color', '#1D9E75') }}" required
                class="mt-1 h-10 w-20 rounded-[8px] border border-gray-300">
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                Create company
            </button>
            <a href="{{ route('organizations.index') }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>
@endsection
