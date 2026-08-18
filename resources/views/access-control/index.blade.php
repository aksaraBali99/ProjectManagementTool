@extends('layouts.authenticated')

@section('title', 'Access control — FounderOS')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Access control</h1>
    <p class="mt-1 text-[10px] uppercase tracking-[0.06em] text-gray-500">
        Grant staff access to departments, per company
    </p>

    @if (session('status'))
        <div class="mt-3 rounded-[8px] bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">No active companies yet — add one under Settings → Companies.</p>
    @else
        {{-- Company tabs, active underline in that company's own accent color --}}
        <div class="mt-4 flex gap-1 border-b border-gray-200">
            @foreach ($organizations as $tab)
                @php $isActiveTab = $organization && $organization->id === $tab->id @endphp
                <a href="{{ route('access-control.index', $tab) }}"
                   style="{{ $isActiveTab ? 'border-color: '.$tab->accent_color : '' }}"
                   class="border-b-2 px-4 py-2 text-[12px] {{ $isActiveTab ? 'font-medium text-[#1F2937]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    {{ $tab->name }}
                </a>
            @endforeach
        </div>

        @if ($departments->isEmpty())
            <p class="mt-6 text-[12px] text-gray-500">{{ $organization->name }} has no active departments yet.</p>
        @else
            <div class="mt-4 overflow-x-auto rounded-[10px] border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Staff</th>
                            @foreach ($departments as $department)
                                <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">
                                    <span class="mr-1 inline-block h-2 w-2 rounded-full align-middle" style="background-color: {{ $department->color }}"></span>
                                    {{ $department->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($staffUsers as $staff)
                            <tr>
                                <td class="flex items-center gap-2 px-3 py-2">
                                    <span class="flex h-[26px] w-[26px] shrink-0 items-center justify-center rounded-full bg-gray-100 text-[10px] font-medium text-gray-600">
                                        {{ collect(explode(' ', $staff->name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}
                                    </span>
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
                                            <label class="relative inline-flex cursor-pointer items-center">
                                                <input
                                                    type="checkbox"
                                                    name="allowed"
                                                    value="1"
                                                    class="peer sr-only"
                                                    {{ $grid[$staff->id][$department->id] ? 'checked' : '' }}
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
@endsection
