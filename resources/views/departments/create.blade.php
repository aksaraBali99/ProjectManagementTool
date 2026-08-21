@extends('layouts.authenticated')

@section('title', 'Add department — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('departments.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Departments</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Add department</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-50 p-3 text-[12px] text-red-700">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('departments.store') }}" class="mt-6 space-y-4" id="create-department-form">
        @csrf

        @php $selectedOrgIds = array_map('intval', old('organization_ids', [])); @endphp
        <div>
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Companies <span class="text-red-600">*</span></span>
            <p class="mt-1 text-[11px] text-gray-500">Select one or more companies — a separate department record is created for each one.</p>
            <div class="mt-2 space-y-2">
                @foreach ($organizations as $organization)
                    <label class="flex items-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-[12px] text-[#1F2937]">
                        <input type="checkbox" name="organization_ids[]" value="{{ $organization->id }}" class="org-checkbox rounded border-gray-300 text-[#1D9E75] focus:ring-[#1D9E75]"
                            {{ in_array($organization->id, $selectedOrgIds, true) ? 'checked' : '' }}>
                        {{ $organization->name }}
                    </label>
                @endforeach
            </div>
            <p id="organizations-error" class="field-error mt-1 text-[11px] text-red-600" style="display: none;"></p>
        </div>

        <div>
            <label for="color" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Color</label>
            <input id="color" name="color" type="color" value="{{ old('color', '#1D9E75') }}" required
                class="mt-1 h-10 w-20 rounded-md border border-gray-300">
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                Create department
            </button>
            <a href="{{ route('departments.index') }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>

    <script>
        (function () {
            const checkboxes = document.querySelectorAll('.org-checkbox');
            const error = document.getElementById('organizations-error');

            function validate(force) {
                const hasSelection = Array.from(checkboxes).some(function (cb) { return cb.checked; });
                if (! hasSelection && force) {
                    error.textContent = 'Select at least one company.';
                    error.style.display = '';
                } else if (hasSelection) {
                    error.style.display = 'none';
                }
                return hasSelection;
            }

            checkboxes.forEach(function (cb) {
                cb.addEventListener('change', function () { validate(true); });
            });

            document.getElementById('create-department-form').addEventListener('submit', function (event) {
                if (! validate(true)) {
                    event.preventDefault();
                }
            });
        })();
    </script>
</div>
@endsection
