@extends('layouts.authenticated')

@section('title', 'Departments — FounderOS')

@section('content')
<div class="mx-auto max-w-4xl">
    <a href="{{ route('settings.index') }}" class="text-xs text-gray-500 hover:underline">← Settings</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">Departments</h1>
        <a href="{{ route('departments.create') }}"
           class="rounded-md bg-[#1D9E75] px-4 py-2 text-sm font-medium text-white hover:bg-[#0F6E56]">
            + Add department
        </a>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-[#E1F5EE] p-3 text-sm text-[#085041]">{{ session('status') }}</div>
    @endif

    @if ($errors->has('department'))
        <div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('department') }}</div>
    @endif

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Name</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Company</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Color</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Tasks</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach ($departments as $department)
                    <tr>
                        <td class="px-4 py-3 text-gray-900">{{ $department->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $department->organization->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block h-4 w-4 rounded-full align-middle" style="background-color: {{ $department->color }}"></span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $department->tasks_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('departments.edit', $department) }}" class="text-[#1D9E75] hover:underline">Edit</a>
                            <form method="POST" action="{{ route('departments.destroy', $department) }}" class="inline"
                                  onsubmit="return confirm('Delete {{ $department->name }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
