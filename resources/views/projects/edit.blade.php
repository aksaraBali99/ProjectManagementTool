@extends('layouts.authenticated')

@section('title', 'Edit project — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('projects.index', $organization) }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Projects</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Edit project</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-[8px] bg-red-50 p-3 text-[12px] text-red-700">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="mt-4 rounded-[8px] bg-[#E1F5EE] p-3 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('projects.update', $project) }}" class="mt-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Company</span>
            <p class="mt-1 text-[12px] font-medium text-[#1F2937]">{{ $organization->name }}</p>
        </div>

        <div>
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Project name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $project->name) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="description" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Description</label>
            <textarea id="description" name="description" rows="3" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">{{ old('description', $project->description) }}</textarea>
        </div>

        <div>
            <label for="client" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Client</label>
            <input id="client" name="client" type="text" value="{{ old('client', $project->client_name) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            <p class="mt-1 text-[11px] text-gray-500">Enter the client's name, or "internal" if this is an internal project.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="status" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</label>
                <select id="status" name="status" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    <option value="open" {{ old('status', $project->status) === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ old('status', $project->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div>
                <label for="priority" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority</label>
                <select id="priority" name="priority" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    <option value="high" {{ old('priority', $project->priority) === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ old('priority', $project->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ old('priority', $project->priority) === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
        </div>

        <div>
            <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Assigned staff</span>
            <p class="mt-1 text-[11px] text-gray-500">Only members of {{ $organization->name }} can be assigned.</p>

            @php $selectedStaff = old('staff', $assignedStaffIds) @endphp

            <div id="staff-rows" class="mt-2 space-y-2">
                @forelse ($selectedStaff as $staffId)
                    <div class="flex items-center gap-2">
                        <select name="staff[]" class="staff-select flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                            <option value="">Select staff…</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}" {{ (int) $staffId === $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="remove-staff-row text-[11px] text-gray-500 hover:underline">Remove</button>
                    </div>
                @empty
                    <div class="flex items-center gap-2">
                        <select name="staff[]" class="staff-select flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                            <option value="">Select staff…</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="remove-staff-row text-[11px] text-gray-500 hover:underline">Remove</button>
                    </div>
                @endforelse
            </div>

            <button type="button" id="add-staff-btn" class="mt-2 text-[11px] font-medium text-[#1D9E75] hover:underline">
                + Add staff
            </button>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                Save changes
            </button>
            <a href="{{ route('projects.index', $organization) }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>

    @if (! empty(auth()->user()->manageableOrganizationIds()))
        <a href="{{ route('projects.template', $project) }}" class="mt-4 inline-block text-[11px] text-gray-500 hover:underline">
            Use as template for another company
        </a>
    @endif
</div>

<script>
    (function () {
        const members = @json($members->map(fn ($member) => ['id' => $member->id, 'name' => $member->name])->values());
        const staffRows = document.getElementById('staff-rows');
        const addStaffBtn = document.getElementById('add-staff-btn');

        function addRow() {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2';

            const select = document.createElement('select');
            select.name = 'staff[]';
            select.className = 'staff-select flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]';
            select.innerHTML = '<option value="">Select staff…</option>';
            members.forEach(function (member) {
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.name;
                select.appendChild(option);
            });

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-staff-row text-[11px] text-gray-500 hover:underline';
            removeBtn.textContent = 'Remove';

            row.appendChild(select);
            row.appendChild(removeBtn);
            staffRows.appendChild(row);
        }

        addStaffBtn.addEventListener('click', addRow);

        staffRows.addEventListener('click', function (event) {
            const removeBtn = event.target.closest('.remove-staff-row');
            if (removeBtn) {
                removeBtn.closest('div').remove();
            }
        });
    })();
</script>
@endsection
