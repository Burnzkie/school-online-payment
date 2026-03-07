{{-- resources/views/parent/notifications.blade.php --}}
@extends('parent.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-400">
        {{ $notifications->total() }} notification{{ $notifications->total() != 1 ? 's' : '' }}
    </p>
    @php $unreadCount = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
    @if($unreadCount > 0)
    <form method="POST" action="{{ route('parent.notifications.readAll') }}">
        @csrf
        <button type="submit"
                class="text-sm text-indigo-500 hover:text-indigo-700 transition flex items-center gap-2 font-medium">
            <i class="fas fa-check-double"></i> Mark all as read
        </button>
    </form>
    @endif
</div>

<div class="space-y-3">
    @forelse($notifications as $notif)
    <div data-notif="{{ $notif->id }}"
         class="bg-white border rounded-2xl shadow-sm p-5 flex gap-4 transition-colors cursor-pointer
                {{ !$notif->is_read ? 'border-indigo-200 bg-indigo-50' : 'border-gray-100' }}"
         @if(!$notif->is_read) onclick="markRead({{ $notif->id }}, this)" @endif>
        <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
             {{ !$notif->is_read ? 'bg-indigo-100' : 'bg-gray-100' }}">
            <i class="fas {{ !$notif->is_read ? 'fa-bell text-indigo-500' : 'fa-bell-slash text-gray-400' }}"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm {{ !$notif->is_read ? 'font-semibold text-gray-800' : 'text-gray-500' }}">
                {{ $notif->message }}
            </p>
            <p class="text-xs text-gray-400 mt-1.5">
                {{ $notif->created_at->diffForHumans() }}
            </p>
        </div>
        @if(!$notif->is_read)
        <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0" style="background: #4f46e5;"></div>
        @endif
    </div>
    @empty
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-bell-slash text-2xl text-gray-300"></i>
        </div>
        <p class="text-gray-500 font-medium">No notifications yet</p>
        <p class="text-gray-300 text-sm mt-1">You'll be notified about payments, due dates, and more.</p>
    </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endif

@endsection 