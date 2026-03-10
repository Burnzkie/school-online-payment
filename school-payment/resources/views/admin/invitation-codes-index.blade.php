@extends('admin.layouts.admin-app')
@section('title', 'Invitation Codes')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Invitation Codes</h1>
            <p class="text-sm text-gray-500 mt-0.5">Generate codes for Cashier, Treasurer, and Parent registration.</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Generate Form --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="text-base font-bold text-gray-800 mb-4">Generate New Code</h2>
        <form method="POST" action="{{ route('admin.invitation-codes.store') }}"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-1.5">Role *</label>
                <select name="role" required
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                    <option value="">Select role</option>
                    <option value="cashier">Cashier</option>
                    <option value="treasurer">Treasurer</option>
                    <option value="parent">Parent</option>
                </select>
                @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-1.5">Label <span class="font-normal normal-case">(optional)</span></label>
                <input type="text" name="label" placeholder="e.g. For Juan dela Cruz"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-1.5">Expires In (days)</label>
                <input type="number" name="expires_in_days" value="7" min="1" max="365"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-1.5">Quantity</label>
                <input type="number" name="quantity" value="1" min="1" max="20"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
            </div>

            <div>
                <button type="submit"
                        class="w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                        style="background: linear-gradient(135deg,#4f46e5,#7c3aed);">
                    Generate
                </button>
            </div>
        </form>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-3 items-center">
        <select name="role" onchange="this.form.submit()"
                class="px-3 py-2 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 focus:outline-none focus:border-indigo-400">
            <option value="">All Roles</option>
            <option value="cashier"   {{ request('role') === 'cashier'   ? 'selected' : '' }}>Cashier</option>
            <option value="treasurer" {{ request('role') === 'treasurer' ? 'selected' : '' }}>Treasurer</option>
            <option value="parent"    {{ request('role') === 'parent'    ? 'selected' : '' }}>Parent</option>
        </select>
        <select name="status" onchange="this.form.submit()"
                class="px-3 py-2 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 focus:outline-none focus:border-indigo-400">
            <option value="">All Status</option>
            <option value="unused" {{ request('status') === 'unused' ? 'selected' : '' }}>Unused</option>
            <option value="used"   {{ request('status') === 'used'   ? 'selected' : '' }}>Used</option>
        </select>
    </form>

    {{-- Codes Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-widest text-slate-400">Code</th>
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-widest text-slate-400">Role</th>
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-widest text-slate-400">Label</th>
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-widest text-slate-400">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-widest text-slate-400">Expires</th>
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-widest text-slate-400">Used By</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($codes as $code)
                <tr class="hover:bg-slate-50 transition-colors">
                    {{-- Code --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-gray-800 tracking-widest text-sm">{{ $code->code }}</span>
                            <button type="button" onclick="copyCode('{{ $code->code }}', this)"
                                    class="p-1 rounded-lg text-gray-300 hover:text-indigo-500 hover:bg-indigo-50 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                </svg>
                            </button>
                        </div>
                    </td>

                    {{-- Role badge --}}
                    <td class="px-5 py-3.5">
                        @php
                            $roleColors = [
                                'cashier'   => 'bg-emerald-100 text-emerald-700',
                                'treasurer' => 'bg-amber-100 text-amber-700',
                                'parent'    => 'bg-sky-100 text-sky-700',
                            ];
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $roleColors[$code->role] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($code->role) }}
                        </span>
                    </td>

                    {{-- Label --}}
                    <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $code->label ?? '—' }}</td>

                    {{-- Status --}}
                    <td class="px-5 py-3.5">
                        @if($code->isUsed())
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-500">Used</span>
                        @elseif($code->isExpired())
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-600">Expired</span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">Active</span>
                        @endif
                    </td>

                    {{-- Expires --}}
                    <td class="px-5 py-3.5 text-xs text-gray-400">
                        {{ $code->expires_at ? $code->expires_at->format('M d, Y') : 'Never' }}
                    </td>

                    {{-- Used by --}}
                    <td class="px-5 py-3.5 text-xs text-gray-500">
                        @if($code->usedBy)
                            <span class="font-medium text-gray-700">{{ $code->usedBy->name }} {{ $code->usedBy->last_name }}</span>
                            <span class="block text-gray-400">{{ $code->used_at->format('M d, Y') }}</span>
                        @else
                            —
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-3.5 text-right">
                        @if(!$code->isUsed())
                        <form method="POST" action="{{ route('admin.invitation-codes.destroy', $code) }}"
                              onsubmit="return confirm('Revoke this invitation code?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors">
                                Revoke
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">No invitation codes yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($codes->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $codes->links() }}
        </div>
        @endif
    </div>

</div>

<script>
function copyCode(code, btn) {
    navigator.clipboard.writeText(code).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '✅';
        setTimeout(() => btn.innerHTML = orig, 2000);
    });
}
</script>
@endsection