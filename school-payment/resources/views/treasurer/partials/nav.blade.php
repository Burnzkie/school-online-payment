{{-- resources/views/treasurer/partials/nav.blade.php --}}
@php
$nav = [
    ['route' => 'treasurer.dashboard',    'icon' => '📊', 'label' => 'Dashboard',      'desc' => 'Revenue overview'],
    ['route' => 'treasurer.fees',         'icon' => '🏷️',  'label' => 'Fee Management', 'desc' => 'Assign & manage fees'],
    ['route' => 'treasurer.payments',     'icon' => '💳', 'label' => 'Payments',        'desc' => 'Transaction history'],
    ['route' => 'treasurer.students',     'icon' => '🎓', 'label' => 'Students',        'desc' => 'Accounts & balances'],
    ['route' => 'treasurer.clearances',   'icon' => '🚫', 'label' => 'Clearances',      'desc' => 'Hold & clear students'],
    ['route' => 'treasurer.scholarships', 'icon' => '🎓', 'label' => 'Scholarships',    'desc' => 'Discounts & aid'],
    ['route' => 'treasurer.aging',        'icon' => '📉', 'label' => 'Aging Report',    'desc' => '30/60/90 day buckets'],
    ['route' => 'treasurer.reports',      'icon' => '📈', 'label' => 'Reports',         'desc' => 'Analytics & exports'],
    ['route' => 'treasurer.profile',      'icon' => '👤', 'label' => 'Profile',         'desc' => 'My account'],
];
@endphp

{{-- User card --}}
<div class="m-4 p-4 rounded-2xl bg-indigo-50 border border-indigo-100">
    <div class="flex items-center gap-3">
        @if(filled(auth()->user()->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->profile_picture))
            <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->profile_picture) }}"
                 class="rounded-full object-cover flex-shrink-0"
                 style="width:32px; height:32px; min-width:32px; min-height:32px; max-width:32px; max-height:32px;">
        @else
            <div class="rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0 shadow-sm"
                 style="width:32px; height:32px; min-width:32px; background: linear-gradient(135deg, #4f46e5, #6366f1);">
                {{ strtoupper(substr(auth()->user()->name ?? 'T', 0, 1)) }}
            </div>
        @endif
        <div class="min-w-0">
            <p class="text-gray-800 font-bold text-sm leading-tight truncate">{{ auth()->user()->name ?? 'Treasurer' }}</p>
            <p class="text-xs font-semibold mt-0.5 text-indigo-500">Treasurer</p>
        </div>
    </div>
</div>

{{-- Nav links --}}
<nav class="flex-1 px-4 pb-4 space-y-0.5">
    @foreach($nav as $item)
        <a href="{{ route($item['route']) }}"
           @if(isset($mobile) && $mobile) @click="mobileMenuOpen = false" @endif
           class="nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
            <span class="nav-icon">{{ $item['icon'] }}</span>
            <div class="min-w-0">
                <p class="text-sm font-bold leading-tight">{{ $item['label'] }}</p>
                <p class="text-xs mt-0.5 text-gray-400 opacity-70">{{ $item['desc'] }}</p>
            </div>
            @if(request()->routeIs($item['route'].'*'))
                <svg class="w-4 h-4 ml-auto text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            @endif
        </a>
    @endforeach
</nav>

{{-- Period widget --}}
<div class="m-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
    <div class="flex items-center gap-2 mb-2">
        <span class="w-2 h-2 rounded-full col-pulse bg-indigo-400"></span>
        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Current Period</p>
    </div>
    <p class="font-bold text-gray-800 text-sm">
        S.Y. {{ date('n') >= 8 ? date('Y').'-'.(date('Y')+1) : (date('Y')-1).'-'.date('Y') }}
    </p>
    <p class="text-xs mt-1 text-indigo-500">Finance Management</p>
</div>

<div class="px-4 py-3 text-center border-t border-gray-100">
    <p class="text-xs text-gray-300">PAC © {{ date('Y') }}</p>
</div>