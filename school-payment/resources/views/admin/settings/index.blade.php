{{-- resources/views/admin/settings/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Settings')

@section('content')

<div class="a-fade">
    <h2 class="text-xl font-bold text-gray-800">System Settings</h2>
    <p class="text-sm mt-0.5 text-gray-400">Configure system-wide behavior, security, and integrations</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 a-fade a-d1">

    {{-- General --}}
    <div class="a-card p-6 space-y-5">
        <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">General Settings</h3>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">School Name</label>
                <input name="school_name" value="Philippine Advent College" class="a-input">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Active School Year</label>
                <input name="school_year" value="{{ date('Y').'-'.(date('Y')+1) }}" class="a-input">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Active Semester</label>
                <select name="semester" class="a-input a-select">
                    @foreach(['1'=>'1st Semester','2'=>'2nd Semester','summer'=>'Summer'] as $v=>$l)
                    <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="a-btn-primary w-full">Save General Settings</button>
        </form>
    </div>

    {{-- Late Fee Config --}}
    <div class="a-card p-6 space-y-5">
        <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Late Fee Rules</h3>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Grace Period (days)</label>
                <input name="grace_days" type="number" min="0" value="7" class="a-input">
                <p class="text-xs mt-1 text-gray-400">Days after due date before penalty applies.</p>
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Late Fee Percentage (%)</label>
                <input name="late_fee_pct" type="number" step="0.01" min="0" value="2" class="a-input">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Max Late Fee (₱)</label>
                <input name="max_late_fee" type="number" step="0.01" min="0" value="500" class="a-input">
            </div>
            <button type="submit" class="a-btn-primary w-full">Save Late Fee Rules</button>
        </form>
    </div>

    {{-- Payment Gateways --}}
    <div class="a-card p-6 space-y-4">
        <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Payment Gateways</h3>
        @foreach([['GCash','💚',true],['PayMaya','💜',true],['Bank Transfer (BDO)','🏦',true],['Credit/Debit Card','💳',false],['Cheque','📝',true]] as [$name,$icon,$enabled])
        <div class="flex items-center justify-between py-2 border-b border-gray-50">
            <div class="flex items-center gap-3">
                <span class="text-xl">{{ $icon }}</span>
                <span class="text-sm font-semibold text-gray-700">{{ $name }}</span>
            </div>
            <div x-data="{ on: {{ $enabled?'true':'false' }} }">
                <button type="button" @click="on=!on"
                        :class="on ? 'bg-indigo-500' : 'bg-gray-200'"
                        class="relative w-10 h-5 rounded-full transition-colors">
                    <span :class="on ? 'translate-x-5' : 'translate-x-0.5'"
                          class="inline-block w-4 h-4 bg-white rounded-full shadow absolute top-0.5 transition-transform"></span>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Security --}}
    <div class="a-card p-6 space-y-4">
        <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Security</h3>
        @foreach([
            ['Two-Factor Authentication (Admin)',true],
            ['Require strong passwords',true],
            ['Automated overdue reminders (email)',true],
            ['Audit logging for all admin actions',true],
            ['Session timeout (30 min)',false],
        ] as [$label,$enabled])
        <div class="flex items-center justify-between py-2 border-b border-gray-50">
            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
            <div x-data="{ on: {{ $enabled?'true':'false' }} }">
                <button type="button" @click="on=!on"
                        :class="on ? 'bg-indigo-500' : 'bg-gray-200'"
                        class="relative w-10 h-5 rounded-full transition-colors">
                    <span :class="on ? 'translate-x-5' : 'translate-x-0.5'"
                          class="inline-block w-4 h-4 bg-white rounded-full shadow absolute top-0.5 transition-transform"></span>
                </button>
            </div>
        </div>
        @endforeach

        <div class="mt-4 pt-3 border-t border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">System Info</p>
            <div class="space-y-1.5 text-xs text-gray-400">
                <div class="flex justify-between"><span>Laravel Version</span><span class="font-mono-num text-gray-700">{{ app()->version() }}</span></div>
                <div class="flex justify-between"><span>PHP Version</span><span class="font-mono-num text-gray-700">{{ PHP_VERSION }}</span></div>
                <div class="flex justify-between"><span>Server Time</span><span class="font-mono-num text-gray-700">{{ now()->format('M d, Y H:i') }}</span></div>
            </div>
        </div>
    </div>

</div>
@endsection