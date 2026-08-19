@extends('layouts.authenticated')

@section('title', 'Add project — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('projects.index', $organization) }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Projects</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Add project</h1>

    @isset($templateName)
        <p class="mt-1 text-[11px] text-gray-500">
            Pre-filled from an existing project. Review the details, then set a client and assign staff for this company.
        </p>
    @endisset

    <form method="POST" action="{{ route('projects.store') }}" class="mt-6 space-y-4" id="create-project-form" novalidate>
        @csrf

        <div>
            <label for="organization_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Company</label>
            <select id="organization_id" name="organization_id" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                @foreach ($organizations as $org)
                    <option value="{{ $org->id }}" {{ (int) old('organization_id', $organization->id) === $org->id ? 'selected' : '' }}>
                        {{ $org->name }}
                    </option>
                @endforeach
            </select>
            @error('organization_id')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Project name <span class="text-red-600">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name', $templateName ?? '') }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('name')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Description <span class="text-red-600">*</span></label>
            <textarea id="description" name="description" rows="3" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">{{ old('description', $templateDescription ?? '') }}</textarea>
            @error('description')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="client" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Client <span class="text-red-600">*</span></label>
            <input id="client" name="client" type="text" value="{{ old('client') }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            <p class="mt-1 text-[11px] text-gray-500">Enter the client's name, or "internal" if this is an internal project.</p>
            @error('client')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="status" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</label>
                <select id="status" name="status" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    <option value="open" {{ old('status', 'open') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                @error('status')
                    <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="priority" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority</label>
                @php $priorityValue = old('priority', $templatePriority ?? 'medium'); @endphp
                <select id="priority" name="priority" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    <option value="high" {{ $priorityValue === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ $priorityValue === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ $priorityValue === 'low' ? 'selected' : '' }}>Low</option>
                </select>
                @error('priority')
                    <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Assigned staff</span>
            <p class="mt-1 text-[11px] text-gray-500">Only members of the selected company can be assigned.</p>

            <div id="staff-rows" class="mt-2 space-y-2">
                @forelse (old('staff', []) as $staffId)
                    <div class="flex items-center gap-2">
                        <select name="staff[]" class="staff-select flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]" data-selected="{{ $staffId }}"></select>
                        <button type="button" class="remove-staff-row text-[11px] text-gray-500 hover:underline">Remove</button>
                    </div>
                @empty
                    <div class="flex items-center gap-2">
                        <select name="staff[]" class="staff-select flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]"></select>
                        <button type="button" class="remove-staff-row text-[11px] text-gray-500 hover:underline">Remove</button>
                    </div>
                @endforelse
            </div>

            <button type="button" id="add-staff-btn" class="mt-2 text-[11px] font-medium text-[#1D9E75] hover:underline">
                + Add staff
            </button>
            @if ($errors->has('staff.*'))
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $errors->first('staff.*') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                Create project
            </button>
            <a href="{{ route('projects.index', $organization) }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>

@include('users._inline-validation')

<script>
    (function () {
        const membersByOrg = @json($organizationMembers);
        const orgSelect = document.getElementById('organization_id');
        const staffRows = document.getElementById('staff-rows');
        const addStaffBtn = document.getElementById('add-staff-btn');

        function currentMembers() {
            return membersByOrg[orgSelect.value] || [];
        }

        function buildOptions(select, selectedId) {
            select.innerHTML = '<option value="">Select staff…</option>';
            currentMembers().forEach(function (member) {
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.name;
                if (String(member.id) === String(selectedId)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }

        function addRow() {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2';

            const select = document.createElement('select');
            select.name = 'staff[]';
            select.className = 'staff-select flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]';
            buildOptions(select, null);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-staff-row text-[11px] text-gray-500 hover:underline';
            removeBtn.textContent = 'Remove';

            row.appendChild(select);
            row.appendChild(removeBtn);
            staffRows.appendChild(row);
        }

        document.querySelectorAll('.staff-select').forEach(function (select) {
            buildOptions(select, select.dataset.selected);
        });

        addStaffBtn.addEventListener('click', addRow);

        staffRows.addEventListener('click', function (event) {
            const removeBtn = event.target.closest('.remove-staff-row');
            if (removeBtn) {
                removeBtn.closest('div').remove();
            }
        });

        orgSelect.addEventListener('change', function () {
            document.querySelectorAll('.staff-select').forEach(function (select) {
                buildOptions(select, select.value);
            });
        });
    })();
</script>

@include('users._unsaved-changes-guard', ['formId' => 'create-project-form'])
@endsection
