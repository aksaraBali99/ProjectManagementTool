@extends('layouts.authenticated')

@section('title', 'Edit company — FounderOS')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('organizations.index') }}" class="text-xs text-gray-500 hover:underline">← Companies</a>

    <h1 class="mt-2 text-xl font-semibold text-gray-900">Edit company</h1>

    @if ($errors->any() && ! $errors->has('confirm_name'))
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

    <div class="mt-10 rounded-md border border-red-200 bg-red-50 p-4">
        <h2 class="text-sm font-medium text-red-800">Delete this company</h2>
        <p class="mt-1 text-xs text-red-700">
            This permanently deletes {{ $organization->name }} and everything under it — departments, projects,
            tasks, documents, and access grants. This cannot be undone.
        </p>
        <button type="button" onclick="document.getElementById('delete-modal').showModal()"
            class="mt-3 rounded-md border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
            Delete company
        </button>
    </div>

    <dialog id="delete-modal" class="w-full max-w-sm rounded-lg border border-gray-200 p-6 shadow-lg backdrop:bg-black/30">
        <h2 class="text-lg font-semibold text-gray-900">Delete {{ $organization->name }}?</h2>

        @if ($errors->has('confirm_name'))
            <div class="mt-3 rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('confirm_name') }}</div>
        @endif

        <form method="POST" action="{{ route('organizations.destroy', $organization) }}" class="mt-4 space-y-4">
            @csrf
            @method('DELETE')

            <div>
                <label for="confirm_name" class="block text-sm font-medium text-gray-700">
                    Type <strong>{{ $organization->name }}</strong> to confirm
                </label>
                <input id="confirm_name" name="confirm_name" type="text" required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('delete-modal').close()" class="text-sm text-gray-600 hover:underline">
                    Cancel
                </button>
                <button type="submit" id="delete-submit-btn" disabled
                    class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Delete permanently
                </button>
            </div>
        </form>
    </dialog>
</div>

<script>
    (function () {
        const input = document.getElementById('confirm_name');
        const btn = document.getElementById('delete-submit-btn');
        const expected = @json($organization->name);

        input.addEventListener('input', function () {
            btn.disabled = input.value !== expected;
        });

        @if ($errors->has('confirm_name'))
            document.getElementById('delete-modal').showModal();
        @endif
    })();
</script>
@endsection
