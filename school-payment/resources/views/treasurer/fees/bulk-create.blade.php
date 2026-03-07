{{-- resources/views/treasurer/fees/bulk-create.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Batch Assign Fee')
@section('content')
<div class="max-w-3xl mx-auto space-y-6 fade-up">

    <div class="flex items-center gap-3">
        <a href="{{ route('treasurer.fees') }}" class="btn-secondary px-3 py-2 rounded-xl text-sm">← Back</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Batch Assign Fee</h1>
            <p class="text-sm text-gray-400">Assign a fee to a group of students at once</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl text-sm bg-red-50 border border-red-100 text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="section-card p-6">
        <form method="POST" action="{{ route('treasurer.fees.bulk-store') }}" class="space-y-6" id="batchFeeForm">
            @csrf

            {{-- ── STEP 1: Academic Period ── --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-3 text-gray-400">① Academic Period</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">School Year *</label>
                        <input type="text" name="school_year" value="{{ old('school_year', date('n') >= 8 ? date('Y').'-'.(date('Y')+1) : (date('Y')-1).'-'.date('Y')) }}" placeholder="e.g. 2025-2026" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Semester *</label>
                        <select name="semester" class="form-input" required>
                            <option value="1"      {{ old('semester')=='1'      ? 'selected':'' }}>1st Semester</option>
                            <option value="2"      {{ old('semester')=='2'      ? 'selected':'' }}>2nd Semester</option>
                            <option value="summer" {{ old('semester')=='summer' ? 'selected':'' }}>Summer</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ── STEP 2: Target Group ── --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-3 text-gray-400">② Target Group</p>
                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Level Group *</label>
                    <select name="level_group" id="levelGroup" class="form-input" required onchange="handleLevelChange(this.value)">
                        <option value="">— Select Level Group —</option>
                        <option value="Kinder"       {{ old('level_group')=='Kinder'       ? 'selected':'' }}>Kinder</option>
                        <option value="Elementary"   {{ old('level_group')=='Elementary'   ? 'selected':'' }}>Elementary</option>
                        <option value="Junior High"  {{ old('level_group')=='Junior High'  ? 'selected':'' }}>Junior High School</option>
                        <option value="Senior High"  {{ old('level_group')=='Senior High'  ? 'selected':'' }}>Senior High School</option>
                        <option value="College"      {{ old('level_group')=='College'      ? 'selected':'' }}>College</option>
                    </select>
                </div>
                <div id="yearLevelWrap" class="mb-4" style="display:none;">
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Year Level <span class="text-xs normal-case font-normal text-gray-400">(leave blank = all year levels in group)</span></label>
                    <select name="year_level" id="yearLevel" class="form-input">
                        <option value="">— All Year Levels —</option>
                    </select>
                </div>
                <div id="strandWrap" class="mb-4" style="display:none;">
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Strand <span class="text-xs normal-case font-normal text-gray-400">(leave blank = all strands)</span></label>
                    <select name="strand" id="strand" class="form-input">
                        <option value="">— All Strands —</option>
                        @foreach($strands as $strand)
                            <option value="{{ $strand }}" {{ old('strand')==$strand ? 'selected':'' }}>{{ $strand }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="collegeWrap" style="display:none;" class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Department <span class="text-xs normal-case font-normal text-gray-400">(optional)</span></label>
                        <select name="department" id="department" class="form-input" onchange="handleDepartmentChange(this.value)">
                            <option value="">— All Departments —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ old('department')==$dept ? 'selected':'' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Program <span class="text-xs normal-case font-normal text-gray-400">(optional)</span></label>
                        <select name="program" id="program" class="form-input">
                            <option value="">— All Programs —</option>
                        </select>
                    </div>
                </div>
                <div id="matchCount" class="text-sm px-4 py-2 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600" style="display:none;">
                    <span id="matchText">—</span>
                </div>
            </div>

            {{-- ── STEP 3: Fee Details ── --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-3 text-gray-400">③ Fee Details</p>
                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Fee Name *</label>
                    <input type="text" name="fee_name" value="{{ old('fee_name') }}" placeholder="e.g. Tuition Fee, Lab Fee, Library Fee" class="form-input" required>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Amount (₱) *</label>
                        <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" placeholder="0.00" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Status</label>
                        <select name="status" class="form-input">
                            <option value="active"    {{ old('status','active')=='active'    ? 'selected':'' }}>Active</option>
                            <option value="waived"    {{ old('status')=='waived'             ? 'selected':'' }}>Waived</option>
                            <option value="cancelled" {{ old('status')=='cancelled'          ? 'selected':'' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Description</label>
                    <textarea name="description" rows="3" placeholder="Optional notes about this fee…" class="form-input resize-none">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- ── STEP 4: Skip Duplicates ── --}}
            <div class="flex items-start gap-3 px-4 py-3 rounded-xl bg-gray-50 border border-gray-100">
                <input type="checkbox" name="skip_existing" id="skipExisting" value="1" checked class="mt-1 rounded" style="accent-color:#4f46e5;">
                <label for="skipExisting" class="text-sm text-gray-600">
                    <span class="text-gray-800 font-semibold">Skip students who already have this fee</span><br>
                    If unchecked, duplicate fee records will be created for those students.
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">📋 Assign to Batch</button>
                <a href="{{ route('treasurer.fees') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100">
        <p class="text-sm text-gray-700">
            💡 Need to assign a fee to just one student?
            <a href="{{ route('treasurer.fees.create') }}" class="font-bold underline text-indigo-500">Use Individual Assign →</a>
        </p>
    </div>
</div>

<script>
const yearLevelMap = {
    'Kinder':      ['Nursery', 'Kinder 1', 'Kinder 2'],
    'Elementary':  ['Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6'],
    'Junior High': ['Grade 7','Grade 8','Grade 9','Grade 10'],
    'Senior High': ['Grade 11','Grade 12'],
    'College':     ['1st Year','2nd Year','3rd Year','4th Year','5th Year'],
};
const programsByDept = @json($programsByDepartment ?? []);

function handleLevelChange(level) {
    const yearWrap = document.getElementById('yearLevelWrap');
    const strandWrap = document.getElementById('strandWrap');
    const collegeWrap = document.getElementById('collegeWrap');
    const yearSel = document.getElementById('yearLevel');
    yearWrap.style.display = strandWrap.style.display = collegeWrap.style.display = 'none';
    yearSel.innerHTML = '<option value="">— All Year Levels —</option>';
    if (!level) { hideCount(); return; }
    if (level === 'College') { collegeWrap.style.display = 'grid'; yearWrap.style.display = 'block'; }
    else { yearWrap.style.display = 'block'; if (level === 'Senior High') strandWrap.style.display = 'block'; }
    (yearLevelMap[level] || []).forEach(yl => { const o = document.createElement('option'); o.value = yl; o.textContent = yl; yearSel.appendChild(o); });
    fetchCount();
}

function handleDepartmentChange(dept) {
    const p = document.getElementById('program');
    p.innerHTML = '<option value="">— All Programs —</option>';
    (dept && programsByDept[dept] ? programsByDept[dept] : []).forEach(prog => { const o = document.createElement('option'); o.value = prog; o.textContent = prog; p.appendChild(o); });
    fetchCount();
}

let countTimer = null;
function fetchCount() {
    clearTimeout(countTimer);
    countTimer = setTimeout(() => {
        const level = document.getElementById('levelGroup').value;
        if (!level) { hideCount(); return; }
        const params = new URLSearchParams({ level_group: level, year_level: document.getElementById('yearLevel').value, strand: document.getElementById('strand')?.value||'', department: document.getElementById('department')?.value||'', program: document.getElementById('program')?.value||'' });
        fetch(`{{ route('treasurer.fees.batch-count') }}?${params}`, { headers: {'X-Requested-With':'XMLHttpRequest'} })
            .then(r => r.json()).then(d => { document.getElementById('matchCount').style.display='block'; document.getElementById('matchText').textContent=`✅ ${d.count} student${d.count!==1?'s':''} match this group`; })
            .catch(() => hideCount());
    }, 350);
}
function hideCount() { document.getElementById('matchCount').style.display = 'none'; }
['yearLevel','strand','program'].forEach(id => { const el = document.getElementById(id); if (el) el.addEventListener('change', fetchCount); });
(function() {
    const ol = "{{ old('level_group') }}";
    if (ol) { document.getElementById('levelGroup').value = ol; handleLevelChange(ol); const oy = "{{ old('year_level') }}"; if (oy) document.getElementById('yearLevel').value = oy; }
})();
</script>
@endsection