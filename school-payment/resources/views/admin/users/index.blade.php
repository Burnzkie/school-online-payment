{{-- resources/views/admin/users/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Users & Roles')

@section('content')

<div class="flex items-center justify-between a-fade">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Users &amp; Roles</h2>
        <p class="text-sm mt-0.5 text-gray-400">Manage staff accounts, roles and permissions</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="a-btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Staff
    </a>
</div>

{{-- Role Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 a-fade a-d1">
    @foreach(['admin'=>['🛡️','#4f46e5'],'treasurer'=>['📋','#059669'],'cashier'=>['💵','#0ea5e9'],'parent'=>['👨‍👩‍👧','#d97706']] as $role=>[$icon,$color])
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-gray-800">{{ $roleCounts[$role]->total ?? 0 }}</p>
        <p class="text-xs mt-1 font-semibold" style="color:{{ $color }}">{{ $icon }} {{ ucfirst($role) }}</p>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form class="a-card p-4 flex flex-wrap gap-3 a-fade a-d2" method="GET">
    <input name="q" value="{{ request('q') }}" placeholder="Search name or email…" class="a-input flex-1 min-w-48">
    <select name="role" class="a-input a-select w-44">
        <option value="">All Roles</option>
        @foreach(['admin','treasurer','cashier','parent'] as $r)
        <option value="{{ $r }}" {{ request('role')===$r?'selected':'' }}>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    <button type="submit" class="a-btn-primary px-5">Filter</button>
    <a href="{{ route('admin.users') }}" class="a-btn-secondary">Reset</a>
</form>

{{-- Table --}}
<div class="a-card a-fade a-d3">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead>
                <tr><th>User</th><th>Role</th><th>Phone</th><th>Extra Info</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            @if($u->profile_picture && Storage::disk('public')->exists($u->profile_picture))
                                <img src="{{ Storage::url($u->profile_picture) }}" class="rounded-full object-cover ring-1 ring-indigo-200 flex-shrink-0" style="width:2rem;height:2rem;min-width:2rem;">
                            @else
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                     style="background: linear-gradient(135deg,#4f46e5,#4338ca);">
                                    {{ strtoupper(substr($u->name,0,1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $u->name }} {{ $u->last_name }}</p>
                                <p class="text-xs text-gray-400">{{ $u->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php $rc=['admin'=>'a-badge-violet','treasurer'=>'a-badge-emerald','cashier'=>'a-badge-sky','parent'=>'a-badge-amber']; @endphp
                        <span class="a-badge {{ $rc[$u->role] ?? 'a-badge-gray' }}">{{ ucfirst($u->role) }}</span>
                    </td>
                    <td class="font-mono-num text-xs text-gray-400">{{ $u->phone ?? '—' }}</td>
                    <td class="text-xs text-gray-500">{{ $u->extra_info ?? '—' }}</td>
                    <td class="text-xs text-gray-400">{{ $u->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.edit', $u) }}" class="a-btn-secondary text-xs py-1.5 px-3">Edit</a>
                            <form method="POST" action="{{ route('admin.users.reset-password', $u) }}" onsubmit="return confirm('Reset password for {{ $u->name }}?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="a-btn-secondary text-xs py-1.5 px-3" style="color:#d97706;border-color:#fde68a;">Reset PW</button>
                            </form>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete {{ $u->name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="a-btn-danger text-xs py-1.5 px-3">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-12 text-gray-400">No staff accounts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection