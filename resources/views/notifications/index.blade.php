@extends('layouts.authenticated')

@section('title', 'Notifications — Solava')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Notifications</h1>
    <p class="mt-1 text-[11px] text-gray-500">Everything you've been notified about. Unread items are marked read once you view this page.</p>

    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200">
        <ul class="divide-y divide-gray-100 bg-white">
            @forelse ($notifications as $notification)
                @php $wasUnread = $originallyUnreadIds->contains($notification->id) @endphp
                <li class="flex items-start gap-3 px-4 py-3 {{ $wasUnread ? 'bg-[#ECFDF5]' : '' }}">
                    <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full {{ $wasUnread ? 'bg-brand-600' : 'bg-transparent' }}"></span>
                    <div class="min-w-0 flex-1">
                        @if ($notification->data['link'] ?? null)
                            <a href="{{ $notification->data['link'] }}" class="text-[12px] font-medium text-[#1F2937] hover:underline">
                                {{ $notification->data['message'] ?? 'Notification' }}
                            </a>
                        @else
                            <span class="text-[12px] font-medium text-[#1F2937]">{{ $notification->data['message'] ?? 'Notification' }}</span>
                        @endif
                        <p class="mt-0.5 text-[11px] text-gray-500">{{ $notification->created_at->format('M j, Y g:ia') }}</p>
                    </div>
                </li>
            @empty
                <li class="px-4 py-6 text-center text-[12px] text-gray-500">No notifications yet.</li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
