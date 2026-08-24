@extends('layouts.authenticated')

@section('title', 'Access control — Solava')

@section('content')
<div>
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-[14px] font-medium text-[#1F2937]">Access control</h1>
            <p class="mt-1 text-[10px] uppercase tracking-[0.06em] text-gray-500">
                Grant staff access to departments, per company
            </p>
        </div>
        @if ($organizations->isNotEmpty() && $departments->isNotEmpty())
            <button type="button" id="access-control-edit-toggle"
                class="shrink-0 rounded-md border border-gray-300 px-3 py-1.5 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                Edit
            </button>
        @endif
    </div>

    <p id="access-control-view-only-note" class="mt-2 text-[11px] text-gray-500">
        View-only — for at-a-glance visibility. Click Edit to make changes, or
        <a href="{{ route('users.index') }}" class="text-[#1D9E75] hover:underline">edit an individual user's department access</a> instead.
    </p>
    <p id="access-control-editing-note" class="mt-2 text-[11px] text-[#0F6E56]" style="display: none;">
        Editing — each toggle saves immediately.
    </p>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">No active companies yet — add one under Settings → Companies.</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="access-control.index" />

        @if ($departments->isEmpty())
            <p class="mt-6 text-[12px] text-gray-500">{{ $organization->name }} has no active departments yet.</p>
        @else
            <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="sticky left-0 z-10 bg-gray-50 px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Staff</th>
                            @foreach ($departments as $department)
                                <th class="px-3 py-2 text-center text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">
                                    <span class="mr-1 inline-block h-2 w-2 rounded-full align-middle" style="background-color: {{ $department->color }}"></span>
                                    {{ $department->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($staffUsers as $staff)
                            <tr>
                                <td class="sticky left-0 z-10 flex items-center gap-2 bg-white px-3 py-2">
                                    <x-avatar :user="$staff" size="30px" />
                                    <span class="text-[12px] font-medium text-[#1F2937]">{{ $staff->name }}</span>
                                </td>
                                @foreach ($departments as $department)
                                    <td class="px-3 py-2 text-center">
                                        <form method="POST" action="{{ route('access-control.toggle') }}" class="inline-flex">
                                            @csrf
                                            <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                                            <input type="hidden" name="department_id" value="{{ $department->id }}">
                                            <input type="hidden" name="user_id" value="{{ $staff->id }}">
                                            <input type="hidden" name="allowed" value="0">
                                            <label class="access-control-toggle-label relative inline-flex cursor-not-allowed items-center opacity-60">
                                                <input
                                                    type="checkbox"
                                                    name="allowed"
                                                    value="1"
                                                    class="access-control-toggle peer sr-only"
                                                    {{ $grid[$staff->id][$department->id] ? 'checked' : '' }}
                                                    disabled
                                                    onchange="this.closest('form').submit()"
                                                >
                                                <span class="block h-4 w-7 rounded-full bg-[#CCCCCA] transition-colors peer-checked:bg-[#1D9E75]"></span>
                                                <span class="absolute left-[2px] block h-3 w-3 rounded-full bg-white transition-transform peer-checked:translate-x-3"></span>
                                            </label>
                                        </form>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $departments->count() + 1 }}" class="px-3 py-4 text-center text-[12px] text-gray-500">
                                    No active staff-role users yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>

<script>
    (function () {
        const editBtn = document.getElementById('access-control-edit-toggle');
        if (! editBtn) return;

        const toggles = document.querySelectorAll('.access-control-toggle');
        const labels = document.querySelectorAll('.access-control-toggle-label');
        const viewOnlyNote = document.getElementById('access-control-view-only-note');
        const editingNote = document.getElementById('access-control-editing-note');
        let editing = false;

        function render() {
            toggles.forEach(function (toggle) { toggle.disabled = ! editing; });
            labels.forEach(function (label) {
                label.classList.toggle('cursor-pointer', editing);
                label.classList.toggle('cursor-not-allowed', ! editing);
                label.classList.toggle('opacity-60', ! editing);
            });
            viewOnlyNote.style.display = editing ? 'none' : '';
            editingNote.style.display = editing ? '' : 'none';
            editBtn.textContent = editing ? 'Done editing' : 'Edit';
        }

        editBtn.addEventListener('click', function () {
            editing = ! editing;
            render();
        });
    })();
</script>
@endsection
