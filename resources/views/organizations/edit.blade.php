@extends('layouts.authenticated')

@section('title', 'Edit company — FounderOS')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('organizations.index') }}" class="text-xs text-gray-500 hover:underline">← Companies</a>

    <h1 class="mt-2 text-xl font-semibold text-gray-900">Edit company</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('organizations.update', $organization) }}" class="mt-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $organization->name) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $organization->slug) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="accent_color" class="block text-sm font-medium text-gray-700">Accent color</label>
            <input id="accent_color" name="accent_color" type="color" value="{{ old('accent_color', $organization->accent_color) }}" required
                class="mt-1 h-10 w-20 rounded-md border border-gray-300">
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-md bg-[#1D9E75] px-4 py-2 text-sm font-medium text-white hover:bg-[#0F6E56]">
                Save changes
            </button>
            <a href="{{ route('organizations.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>

    <div class="mt-10 rounded-md border border-gray-200 bg-gray-50 p-4">
        <h2 class="text-sm font-medium text-gray-700">
            {{ $organization->is_active ? 'Deactivate' : 'Activate' }} this company
        </h2>
        <p class="mt-1 text-xs text-gray-500">
            @if ($organization->is_active)
                Staff and management lose access to {{ $organization->name }} immediately — its departments,
                projects, and tasks disappear from their view. Nothing is deleted, and you can reactivate it
                at any time.
            @else
                {{ $organization->name }} is currently inactive and hidden from everyone except owners and
                super admins. Reactivating restores visibility for its members immediately.
            @endif
        </p>
        <form method="POST" action="{{ route('organizations.toggle-active', $organization) }}" class="mt-3">
            @csrf
            @method('PATCH')
            <button type="submit" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                {{ $organization->is_active ? 'Deactivate company' : 'Activate company' }}
            </button>
        </form>
    </div>
</div>
@endsection
