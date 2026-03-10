<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Philippine Advent College – Online Payment</title>
    @include('partials.favicon')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .role-btn.active {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: white;
            transform: scale(1.05);
            border-color: #0ea5e9;
            box-shadow: 0 6px 20px rgba(14,165,233,0.3);
        }
        #student-results::-webkit-scrollbar { width: 4px; }
        #student-results::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
    </style>
</head>
<body class="text-slate-100 min-h-screen flex" style="background: linear-gradient(135deg, #030d1e 0%, #051120 55%, #071a30 100%);">

    <!-- Left illustration panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-cover bg-center relative flex-shrink-0"
         style="background-image: url('{{ asset("images/pac-building.png") }}')">
        <div class="absolute inset-0" style="background: linear-gradient(160deg, rgba(3,13,30,0.42) 0%, rgba(3,13,30,0.55) 40%, rgba(3,13,30,0.82) 75%, rgba(3,13,30,0.93) 100%);"></div>
        <div class="absolute inset-0" style="background: radial-gradient(ellipse 80% 55% at 25% 8%, rgba(14,165,233,0.18) 0%, transparent 60%);"></div>
        <!-- PAC Branding pinned to bottom-left -->
        <div class="absolute bottom-0 left-0 right-0 px-12 pb-12" style="z-index:10;">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-2xl flex-shrink-0"
                     style="background: linear-gradient(135deg, #0ea5e9, #06b6d4); box-shadow: 0 8px 28px rgba(14,165,233,0.45);">🎓</div>
                <div>
                    <p class="text-white font-extrabold text-xl leading-snug">Philippine Advent College</p>
                    <p class="text-xs font-semibold mt-0.5" style="color: #7dd3fc;">"The School That Prepares Students to Serve."</p>
                </div>
            </div>
            <p class="font-extrabold text-white leading-tight mb-1" style="font-size:2.6rem;">School Fees,</p>
            <p class="font-extrabold leading-tight mb-6" style="font-size:2.6rem; background: linear-gradient(135deg,#38bdf8,#22d3ee); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Made Simple.</p>
            <p class="text-sm font-medium mb-7" style="color: rgba(255,255,255,0.55);">Kinder &nbsp;&middot;&nbsp; Elementary &nbsp;&middot;&nbsp; Junior High &nbsp;&middot;&nbsp; Senior High &nbsp;&middot;&nbsp; College</p>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold" style="background: rgba(14,165,233,0.18); border: 1px solid rgba(14,165,233,0.32); color: #7dd3fc;">✅ Online Payment Tracking</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold" style="background: rgba(6,182,212,0.14); border: 1px solid rgba(6,182,212,0.28); color: #67e8f9;">📋 Installment Plans</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.6);">🧾 Official Receipts</span>
            </div>
        </div>
    </div><!-- end left panel -->

    <!-- Right form panel -->
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12" style="background: rgba(3,13,30,0.5);">
        <div class="w-full max-w-lg">

            <!-- Tabs -->
            <div class="flex bg-slate-800/60 backdrop-blur-md rounded-3xl p-1.5 mb-10 shadow-2xl border border-slate-700/50">
                <button type="button" onclick="switchTab(0)" id="tab-login"
                        class="tab-btn flex-1 py-4 text-lg font-semibold rounded-2xl transition-all duration-300">
                    Login
                </button>
                <button type="button" onclick="switchTab(1)" id="tab-register"
                        class="tab-btn flex-1 py-4 text-lg font-semibold rounded-2xl text-white transition-all duration-300" style="background: linear-gradient(135deg, #0ea5e9, #06b6d4);">
                    Register
                </button>
            </div>

            <!-- Login Form -->
            <div id="login-panel" class="form-panel">
                <div class="glass rounded-3xl p-8 lg:p-10 shadow-2xl">
                    <h2 class="text-3xl font-bold mb-8 text-center">Welcome Back</h2>

                    @if (session('status'))
                        <div class="bg-green-900/30 border border-green-600 text-green-200 px-6 py-4 rounded-2xl mb-6 text-center">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                       class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                       placeholder="youremail@example.com">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Password</label>
                                <div class="relative">
                                    <input type="password" name="password" required
                                           class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                    <button type="button" onclick="togglePassword(this)" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <label class="flex items-center">
                                    <input type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-700 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2">Remember me</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-blue-400 hover:text-blue-300 transition">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>
                            <button type="submit"
                                    class="w-full py-5 rounded-3xl font-semibold text-lg transition-all active:scale-[0.98] hover:opacity-90" style="background: linear-gradient(135deg, #0ea5e9, #06b6d4); box-shadow: 0 8px 25px rgba(14,165,233,0.28);">
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Register Form -->
            <div id="register-panel" class="form-panel hidden">
                <div class="glass rounded-3xl p-8 lg:p-10 shadow-2xl max-h-[calc(100vh-12rem)] overflow-y-auto">
                    <h2 class="text-3xl font-bold mb-8 text-center sticky top-0 bg-slate-900/80 backdrop-blur-md py-4 -mx-8 px-8 z-10">
                        Create Account
                    </h2>

                    @if ($errors->any())
                        <div class="bg-red-900/30 border border-red-600 text-red-200 px-6 py-4 rounded-2xl mb-8">
                            <ul class="list-disc pl-5 space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('status'))
                    <div class="flex items-start gap-4 bg-emerald-900/30 border border-emerald-500/50 text-emerald-200 px-6 py-5 rounded-2xl mb-8">
                        <svg class="w-6 h-6 flex-shrink-0 mt-0.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="font-bold text-emerald-300 text-sm">Check Your Email</p>
                            <p class="text-sm mt-1 text-emerald-200/80">{{ session('status') }}</p>
                        </div>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" id="register-form">
                        @csrf

                        <!-- Role selection -->
                        <div class="grid grid-cols-4 gap-3 mb-8" id="role-buttons">
                            <button type="button" onclick="selectRole(this)" data-role="student"
                                    class="role-btn col-span-1 py-3.5 text-sm font-medium rounded-2xl border border-slate-600 hover:border-blue-500 transition">
                                Student
                            </button>
                            <button type="button" onclick="selectRole(this)" data-role="parent"
                                    class="role-btn col-span-1 py-3.5 text-sm font-medium rounded-2xl border border-slate-600 hover:border-sky-400 transition">
                                Parent
                            </button>
                            <button type="button" onclick="selectRole(this)" data-role="treasurer"
                                    class="role-btn col-span-1 py-3.5 text-sm font-medium rounded-2xl border border-slate-600 hover:border-amber-500 transition">
                                Treasurer
                            </button>
                            <button type="button" onclick="selectRole(this)" data-role="cashier"
                                    class="role-btn col-span-1 py-3.5 text-sm font-medium rounded-2xl border border-slate-600 hover:border-emerald-500 transition">
                                Cashier
                            </button>
                        </div>

                        <input type="hidden" name="role" id="role-input" value="">

                        <!-- Common fields: email + password -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Email *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                       class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                       placeholder="youremail@example.com">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Password *</label>
                                <div class="relative">
                                    <input type="password" name="password" required minlength="8"
                                           class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                           placeholder="Minimum 8 characters">
                                    <button type="button" onclick="togglePassword(this)" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Confirm Password *</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" required minlength="8"
                                           class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                           placeholder="Re-enter password">
                                    <button type="button" onclick="togglePassword(this)" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ══════════════════════════════════════
                             STUDENT-SPECIFIC FIELDS
                        ══════════════════════════════════════ --}}
                        <div id="student-form" class="mt-6 space-y-6 hidden">
                            <!-- Age & Education Level -->
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-blue-400">Student Age & Level</h3>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Age *</label>
                                    <input type="number" name="age" id="age" value="{{ old('age') }}" min="3" max="99"
                                           onchange="calculateLevel()" required
                                           class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                           placeholder="Enter age">
                                    <div id="level-badge" class="mt-2 text-sm px-4 py-2 rounded-xl inline-block" style="background: rgba(14,165,233,0.1); color: #7dd3fc; border: 1px solid rgba(14,165,233,0.2);"></div>
                                </div>
                            </div>
                            <!-- Personal Info -->
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-blue-400">Personal Information</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">First Name *</label>
                                        <input type="text" name="name" value="{{ old('name') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="First name" pattern="[A-Za-zÀ-ÿ\s]+" title="Only letters allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Middle Name</label>
                                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="Middle name" pattern="[A-Za-zÀ-ÿ\s]*" title="Only letters allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Last Name *</label>
                                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="Last name" pattern="[A-Za-zÀ-ÿ\s]+" title="Only letters allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Suffix</label>
                                        <input type="text" name="suffix" value="{{ old('suffix') }}" placeholder="Jr, Sr, III"
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Date of Birth *</label>
                                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Gender *</label>
                                        <select name="gender" required
                                                class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                            <option value="">Select Gender</option>
                                            <option value="MALE" {{ old('gender') === 'MALE' ? 'selected' : '' }}>Male</option>
                                            <option value="FEMALE" {{ old('gender') === 'FEMALE' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Nationality</label>
                                        <input type="text" name="nationality" value="{{ old('nationality', 'Filipino') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Mobile Number</label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}"
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="09XX XXX XXXX" pattern="[0-9]{11}" maxlength="11">
                                    </div>
                                </div>
                            </div>
                            <!-- (remaining student fields: level, address, parents - keep existing structure) -->
                            <div class="border-t border-slate-700 pt-6" id="year-level-group" style="display:none">
                                <h3 class="text-lg font-semibold mb-4 text-blue-400">Year / Grade Level</h3>
                                <input type="hidden" name="level_group" id="level_group" value="{{ old('level_group') }}">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Year / Grade</label>
                                    <select name="year_level" id="year-level"
                                            class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                        <option value="">Select Year/Grade</option>
                                    </select>
                                </div>
                                <div id="strand-group" class="mt-4 hidden">
                                    <label class="block text-sm font-medium mb-2">Strand</label>
                                    <select name="strand"
                                            class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                        <option value="">Select Strand</option>
                                        <option value="STEM">STEM</option>
                                        <option value="ABM">ABM</option>
                                        <option value="HUMSS">HUMSS</option>
                                        <option value="GAS">GAS</option>
                                        <option value="TVL">TVL</option>
                                    </select>
                                </div>
                                <div id="college-group" class="mt-4 space-y-4 hidden">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Department</label>
                                        <select name="department" id="department" onchange="populateCourses()"
                                                class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                            <option value="">Select Department</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Course / Program</label>
                                        <select name="program" id="course"
                                                class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                            <option value="">Select Course</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ══════════════════════════════════════
                             PARENT-SPECIFIC FIELDS
                        ══════════════════════════════════════ --}}
                        <div id="parent-form" class="mt-6 space-y-6 hidden">

                            <!-- Personal Information -->
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-sky-300">
                                    <i class="fas fa-user mr-2"></i>Parent / Guardian Information
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">First Name *</label>
                                        <input type="text" name="name" value="{{ old('name') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="First name" pattern="[A-Za-zÀ-ÿ\s]+" title="Only letters allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Middle Name</label>
                                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="Middle name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Last Name *</label>
                                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="Last name" pattern="[A-Za-zÀ-ÿ\s]+" title="Only letters allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Phone / Mobile *</label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="09XX XXX XXXX" pattern="[0-9]{11}" maxlength="11">
                                        <p class="text-xs text-slate-500 mt-1">
                                            <i class="fas fa-info-circle text-sky-300 mr-1"></i>
                                            Used to link you with your child's enrollment records.
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Date of Birth</label>
                                        <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Gender</label>
                                        <select name="gender"
                                                class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition">
                                            <option value="">Select Gender</option>
                                            <option value="MALE" {{ old('gender') === 'MALE' ? 'selected' : '' }}>Male</option>
                                            <option value="FEMALE" {{ old('gender') === 'FEMALE' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Nationality</label>
                                        <input type="text" name="nationality" value="{{ old('nationality', 'Filipino') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="e.g., Filipino">
                                    </div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-sky-300">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Address
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium mb-2">Street / House No.</label>
                                        <input type="text" name="street" value="{{ old('street') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="e.g., 123 Rizal St.">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Barangay</label>
                                        <input type="text" name="barangay" value="{{ old('barangay') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="Barangay">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Municipality</label>
                                        <input type="text" name="municipality" value="{{ old('municipality') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="Municipality">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium mb-2">City / Province</label>
                                        <input type="text" name="city" value="{{ old('city') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition"
                                               placeholder="City or Province">
                                    </div>
                                </div>
                            </div>

                            <!-- Student Linking via Search -->
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-1 text-sky-300">
                                    <i class="fas fa-user-graduate mr-2"></i>Link Your Child's Account
                                </h3>
                                <p class="text-xs text-slate-400 mb-4">
                                    Search for your child's name to link their billing records to your account.
                                    You can also link them later via your profile using your registered phone number.
                                </p>

                                <!-- Search Input -->
                                <div class="relative">
                                    <div class="flex items-center gap-2 bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus-within:border-sky-400 focus-within:ring-2 focus-within:ring-sky-400/30 transition">
                                        <i class="fas fa-search text-slate-400 text-sm flex-shrink-0"></i>
                                        <input type="text" id="student-search-input"
                                               class="flex-1 bg-transparent text-sm focus:outline-none placeholder-slate-500"
                                               placeholder="Type student name to search…"
                                               autocomplete="off"
                                               oninput="searchStudents(this.value)">
                                        <span id="search-spinner" class="hidden">
                                            <i class="fas fa-spinner fa-spin text-sky-300 text-sm"></i>
                                        </span>
                                    </div>

                                    <!-- Dropdown Results -->
                                    <div id="student-results"
                                         class="hidden absolute z-30 w-full mt-2 bg-slate-800 border border-slate-600 rounded-2xl shadow-2xl max-h-56 overflow-y-auto">
                                    </div>
                                </div>

                                <!-- Selected Student Tag -->
                                <div id="selected-student-tag" class="hidden mt-3 flex items-center gap-3 bg-sky-900/30 border border-sky-400/40 rounded-xl px-4 py-3">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-xs font-bold flex-shrink-0" id="selected-avatar">?</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold truncate" id="selected-name">—</p>
                                        <p class="text-xs text-slate-400" id="selected-meta">—</p>
                                    </div>
                                    <button type="button" onclick="clearStudentSelection()"
                                            class="text-slate-400 hover:text-red-400 transition flex-shrink-0">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>

                                {{-- Hidden input sent to the server --}}
                                <input type="hidden" name="linked_student_id" id="linked_student_id" value="">

                                <p class="text-xs text-slate-500 mt-3">
                                    <i class="fas fa-lock text-sky-300 mr-1"></i>
                                    Only students already registered in the system will appear.
                                </p>
                            </div>

                        </div>{{-- end #parent-form --}}

                        {{-- ══════════════════════════════════════
                             TREASURER FIELDS
                        ══════════════════════════════════════ --}}
                        <div id="treasurer-form" class="mt-6 space-y-6 hidden">

                            {{-- Personal Information --}}
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-amber-400">
                                    <i class="fas fa-id-badge mr-2"></i>Treasurer Information
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">First Name *</label>
                                        <input type="text" name="name" value="{{ old('name') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                               placeholder="First name" pattern="[A-Za-zÀ-ÿ\s]+" title="Only letters allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Middle Name</label>
                                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                               placeholder="Middle name">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium mb-2">Last Name *</label>
                                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                               placeholder="Last name" pattern="[A-Za-zÀ-ÿ\s]+" title="Only letters allowed">
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium mb-2">Employee ID *</label>
                                    <input type="text" name="employee_id" value="{{ old('employee_id') }}"
                                           class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                           placeholder="TR-2025-001">
                                </div>
                            </div>

                            {{-- Contact Information --}}
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-amber-400">
                                    <i class="fas fa-phone mr-2"></i>Contact Information
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Mobile Number *</label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                                               class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                               placeholder="09XX-XXX-XXXX">
                                        @error('phone')
                                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Work Email *</label>
                                        <input type="email" name="work_email" value="{{ old('work_email') }}" required
                                               class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                               placeholder="treasurer@pac.edu.ph">
                                        @error('work_email')
                                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-amber-400">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Address
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Street / Barangay *</label>
                                        <input type="text" name="street" value="{{ old('street') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                               placeholder="House No., Street, Barangay">
                                        @error('street')
                                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-2">Municipality / City *</label>
                                            <input type="text" name="municipality" value="{{ old('municipality') }}" required
                                                   class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                                   placeholder="Municipality or City">
                                            @error('municipality')
                                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium mb-2">Province *</label>
                                            <input type="text" name="province" value="{{ old('province') }}" required
                                                   class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                                   placeholder="Province">
                                            @error('province')
                                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-2">ZIP Code</label>
                                            <input type="text" name="zip_code" value="{{ old('zip_code') }}"
                                                   class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                                   placeholder="4-digit ZIP code" maxlength="4" pattern="\d{4}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium mb-2">Country</label>
                                            <input type="text" name="country" value="{{ old('country', 'Philippines') }}"
                                                   class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition"
                                                   placeholder="Philippines">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- ══════════════════════════════════════
                             CASHIER FIELDS
                        ══════════════════════════════════════ --}}
                        <div id="cashier-form" class="mt-6 space-y-6 hidden">

                            {{-- Personal Information --}}
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-emerald-400">
                                    <i class="fas fa-user mr-2"></i>Personal Information
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">First Name *</label>
                                        <input type="text" name="name" value="{{ old('name') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="First name" pattern="[A-Za-zÀ-ÿ\s]+" title="Only letters allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Middle Name</label>
                                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="Middle name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Last Name *</label>
                                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="Last name" pattern="[A-Za-zÀ-ÿ\s]+" title="Only letters allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Suffix</label>
                                        <input type="text" name="suffix" value="{{ old('suffix') }}"
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="Jr, Sr, III">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Date of Birth</label>
                                        <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Gender</label>
                                        <select name="gender"
                                                class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition">
                                            <option value="">Select Gender</option>
                                            <option value="Male"   {{ old('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Contact & Employment --}}
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-emerald-400">
                                    <i class="fas fa-phone mr-2"></i>Contact &amp; Employment
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Mobile Number</label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}"
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="09XX XXX XXXX" pattern="[0-9]{11}" maxlength="11">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Alternate Phone</label>
                                        <input type="tel" name="alternate_phone" value="{{ old('alternate_phone') }}"
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="Landline or secondary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Position / Title *</label>
                                        <input type="text" name="position" value="{{ old('position') }}" required
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="e.g. Head Cashier, Cashier II">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Employee ID</label>
                                        <input type="text" name="employee_id" value="{{ old('employee_id') }}"
                                               class="w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="e.g. EMP-2025-001">
                                    </div>
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="border-t border-slate-700 pt-6">
                                <h3 class="text-lg font-semibold mb-4 text-emerald-400">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Address
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium mb-2">Street / House No.</label>
                                        <input type="text" name="street" value="{{ old('street') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="e.g. 123 Rizal St.">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Barangay</label>
                                        <input type="text" name="barangay" value="{{ old('barangay') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="Barangay">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Municipality</label>
                                        <input type="text" name="municipality" value="{{ old('municipality') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="Municipality">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium mb-2">City / Province</label>
                                        <input type="text" name="city" value="{{ old('city') }}"
                                               class="auto-capitalize w-full bg-slate-800 border border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 transition"
                                               placeholder="City or Province">
                                    </div>
                                </div>
                            </div>

                        </div>{{-- end #cashier-form --}}

                        {{-- ══════════════════════════════════════
                             INVITATION CODE (staff & parent only)
                        ══════════════════════════════════════ --}}
                        <div id="invitation-code-wrap" class="hidden mt-6">
                            <div class="border-t border-slate-700 pt-6">
                                <label class="block text-sm font-medium mb-2">
                                    Invitation Code *
                                    <span class="text-slate-400 font-normal text-xs ml-1">— provided by the admin</span>
                                </label>
                                <input type="text" name="invitation_code"
                                       id="invitation_code"
                                       value="{{ old('invitation_code') }}"
                                       placeholder="e.g. ABCD-EFGH-IJKL"
                                       autocomplete="off"
                                       class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 font-mono tracking-widest uppercase focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30 transition text-sky-300"
                                       oninput="this.value = this.value.toUpperCase()">
                                @error('invitation_code')
                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-slate-500 mt-2">
                                    Don't have a code? Contact the PAC administrator.
                                </p>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full mt-8 py-5 rounded-3xl font-semibold text-lg transition-all active:scale-[0.98] hover:opacity-90" style="background: linear-gradient(135deg, #0ea5e9, #06b6d4); box-shadow: 0 8px 25px rgba(14,165,233,0.28);">
                            Create Account
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        let currentRole = '';
        let searchTimeout = null;

        // ── Auto-capitalize ──────────────────────────────────────
        function capitalizeInput(input) {
            const words = input.value.split(' ');
            input.value = words.map(w => w.length ? w.charAt(0).toUpperCase() + w.slice(1).toLowerCase() : w).join(' ');
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.auto-capitalize').forEach(input => {
                input.addEventListener('blur', function() { capitalizeInput(this); });
            });
        });

        // ── Tab switching ────────────────────────────────────────
        function switchTab(index) {
            document.querySelectorAll('.tab-btn').forEach((btn, i) => {
                btn.classList.toggle('bg-blue-600', i === index);
                btn.classList.toggle('text-white', i === index);
            });
            document.querySelectorAll('.form-panel').forEach((panel, i) => {
                panel.classList.toggle('hidden', i !== index);
            });
        }

        // ── Required fields helper ───────────────────────────────
        function setRequiredFields(container, required) {
            if (!container) return;
            container.querySelectorAll('input, select, textarea').forEach(field => {
                if (required) {
                    if (field.dataset.wasRequired === 'true') field.required = true;
                    if (field.dataset.originalName) field.name = field.dataset.originalName;
                } else {
                    field.dataset.wasRequired = field.required ? 'true' : 'false';
                    field.required = false;
                    if (field.name) {
                        field.dataset.originalName = field.name;
                        field.name = '';
                    }
                }
            });
        }

        // ── Role selection ───────────────────────────────────────
        function selectRole(btn) {
            document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentRole = btn.dataset.role;

            const studentForm   = document.getElementById('student-form');
            const parentForm    = document.getElementById('parent-form');
            const treasurerForm = document.getElementById('treasurer-form');
            const cashierForm   = document.getElementById('cashier-form');
            const inviteWrap    = document.getElementById('invitation-code-wrap');
            const inviteInput   = document.getElementById('invitation_code');

            [studentForm, parentForm, treasurerForm, cashierForm].forEach(f => {
                if (f) { f.classList.add('hidden'); setRequiredFields(f, false); }
            });

            // Show/hide invitation code field
            const needsCode = ['parent', 'cashier', 'treasurer'].includes(currentRole);
            inviteWrap.classList.toggle('hidden', !needsCode);
            if (inviteInput) inviteInput.required = needsCode;

            if (currentRole === 'student') {
                studentForm.classList.remove('hidden');
                setRequiredFields(studentForm, true);

            } else if (currentRole === 'parent') {
                parentForm.classList.remove('hidden');
                setRequiredFields(parentForm, true);

            } else if (currentRole === 'treasurer') {
                treasurerForm.classList.remove('hidden');
                setRequiredFields(treasurerForm, true);

            } else if (currentRole === 'cashier') {
                cashierForm.classList.remove('hidden');
                setRequiredFields(cashierForm, true);
            }
        }

        // ── Student search (AJAX) ────────────────────────────────
        function searchStudents(query) {
            const resultsBox = document.getElementById('student-results');
            const spinner    = document.getElementById('search-spinner');

            clearTimeout(searchTimeout);

            if (query.trim().length < 2) {
                resultsBox.classList.add('hidden');
                resultsBox.innerHTML = '';
                spinner.classList.add('hidden');
                return;
            }

            spinner.classList.remove('hidden');

            searchTimeout = setTimeout(async () => {
                try {
                    const res  = await fetch(`/api/students/search?q=${encodeURIComponent(query)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    spinner.classList.add('hidden');

                    if (!data.length) {
                        resultsBox.innerHTML = `
                            <div class="px-5 py-4 text-sm text-slate-400 text-center">
                                <i class="fas fa-search mr-2 text-slate-600"></i>No students found
                            </div>`;
                        resultsBox.classList.remove('hidden');
                        return;
                    }

                    resultsBox.innerHTML = data.map(s => {
                        const initials = (s.name.charAt(0) + (s.last_name ? s.last_name.charAt(0) : '')).toUpperCase();
                        return `
                            <button type="button"
                                    onclick='selectStudent(${JSON.stringify(s)})'
                                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-700/60 transition text-left border-b border-slate-700/40 last:border-0">
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    ${initials}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium">${s.name} ${s.middle_name ?? ''} ${s.last_name ?? ''}</p>
                                    <p class="text-xs text-slate-400">${s.year_level ?? ''} • ${s.level_group ?? ''}</p>
                                </div>
                                ${s.student_id ? `<span class="text-xs text-slate-500 font-mono">${s.student_id}</span>` : ''}
                            </button>`;
                    }).join('');

                    resultsBox.classList.remove('hidden');
                } catch (err) {
                    spinner.classList.add('hidden');
                    resultsBox.innerHTML = `<div class="px-5 py-4 text-sm text-red-400 text-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>Search failed. Try again.</div>`;
                    resultsBox.classList.remove('hidden');
                }
            }, 350);
        }

        function selectStudent(s) {
            document.getElementById('linked_student_id').value = s.id;

            const initials = (s.name.charAt(0) + (s.last_name ? s.last_name.charAt(0) : '')).toUpperCase();
            document.getElementById('selected-avatar').textContent = initials;
            document.getElementById('selected-name').textContent  = [s.name, s.middle_name, s.last_name].filter(Boolean).join(' ');
            document.getElementById('selected-meta').textContent  = [s.year_level, s.level_group].filter(Boolean).join(' • ');

            document.getElementById('selected-student-tag').classList.remove('hidden');
            document.getElementById('student-results').classList.add('hidden');
            document.getElementById('student-search-input').value = '';
        }

        function clearStudentSelection() {
            document.getElementById('linked_student_id').value = '';
            document.getElementById('selected-student-tag').classList.add('hidden');
            document.getElementById('student-search-input').value = '';
        }

        // Close results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#student-search-input') && !e.target.closest('#student-results')) {
                document.getElementById('student-results')?.classList.add('hidden');
            }
        });

        // ── Education data ───────────────────────────────────────
        const educationData = {
            kinder:     { label: 'Kinder',               levels: ['Kindergarten 1', 'Kindergarten 2'] },
            elementary: { label: 'Elementary',            levels: ['Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6'] },
            junior:     { label: 'Junior High School',    levels: ['Grade 7','Grade 8','Grade 9','Grade 10'] },
            senior:     { label: 'Senior High School',    levels: ['Grade 11','Grade 12'] },
            college:    { label: 'College',               levels: ['1st Year','2nd Year','3rd Year','4th Year','5th Year'] }
        };

        const collegeDepartments = {
            "Education":             ["Bachelor of Elementary Education (BEEd)","Bachelor of Secondary Education (BSEd) - Major in English","Bachelor of Secondary Education (BSEd) - Major in Mathematics","Bachelor of Secondary Education (BSEd) - Major in Science","Bachelor of Secondary Education (BSEd) - Major in Filipino"],
            "Business Administration":["BS in Accountancy (BSA)","BS in Business Administration - Major in Financial Management","BS in Business Administration - Major in Marketing Management","BS in Business Administration - Major in Human Resource Management","BS in Entrepreneurship"],
            "Computer Studies":      ["BS in Computer Science (BSCS)","BS in Information Technology (BSIT)","BS in Computer Engineering (BSCpE)","BS in Information Systems (BSIS)"],
            "Engineering":           ["BS in Civil Engineering (BSCE)","BS in Electrical Engineering (BSEE)","BS in Electronics Engineering (BSECE)","BS in Mechanical Engineering (BSME)"],
            "Nursing":               ["BS in Nursing (BSN)"],
            "Theology":              ["Bachelor of Arts in Theology (AB Theology)","Bachelor of Theology (BTh)"]
        };

        function calculateLevel() {
            const age  = parseInt(document.getElementById('age')?.value) || 0;
            const badge = document.getElementById('level-badge');
            const levelGroupInput = document.getElementById('level_group');
            const yearLevelGroup  = document.getElementById('year-level-group');
            if (!badge || age < 3) { yearLevelGroup?.classList.add('hidden'); return; }
            let key = null;
            if (age < 7) key = 'kinder';
            else if (age <= 12) key = 'elementary';
            else if (age <= 16) key = 'junior';
            else key = 'adult';
            if (key === 'adult') {
                badge.innerHTML = `<span class="font-semibold">Choose your level: </span>
                    <select onchange="chooseAdultLevel(this)" class="bg-slate-800 border border-slate-600 rounded px-3 py-1 ml-2 text-emerald-300 outline-none focus:border-blue-500">
                        <option value="">-- Select Level --</option>
                        <option value="senior">Senior High School</option>
                        <option value="college">College</option>
                    </select>`;
                levelGroupInput.value = '';
                yearLevelGroup?.classList.add('hidden');
                document.getElementById('strand-group')?.classList.add('hidden');
                document.getElementById('college-group')?.classList.add('hidden');
                return;
            }
            const data = educationData[key];
            badge.innerHTML = `<span class="font-semibold">Detected Level: </span><span class="text-emerald-400">${data.label}</span>`;
            levelGroupInput.value = data.label;
            yearLevelGroup?.classList.remove('hidden');
            populateYearLevel(data.levels);
            document.getElementById('strand-group')?.classList.add('hidden');
            document.getElementById('college-group')?.classList.add('hidden');
        }

        function chooseAdultLevel(sel) {
            const val  = sel.value;
            const badge = document.getElementById('level-badge');
            const levelGroupInput = document.getElementById('level_group');
            const yearLevelGroup  = document.getElementById('year-level-group');
            if (!val) { yearLevelGroup?.classList.add('hidden'); return; }
            if (val === 'senior') {
                badge.innerHTML = `<span class="font-semibold">Selected Level: </span><span class="text-emerald-400">Senior High School</span>
                    <button type="button" onclick="calculateLevel()" class="ml-2 text-xs text-blue-400 hover:text-blue-300">(change)</button>`;
                levelGroupInput.value = 'Senior High School';
                populateYearLevel(educationData.senior.levels);
                yearLevelGroup?.classList.remove('hidden');
                document.getElementById('strand-group')?.classList.remove('hidden');
                document.getElementById('college-group')?.classList.add('hidden');
            } else {
                badge.innerHTML = `<span class="font-semibold">Selected Level: </span><span class="text-emerald-400">College</span>
                    <button type="button" onclick="calculateLevel()" class="ml-2 text-xs text-blue-400 hover:text-blue-300">(change)</button>`;
                levelGroupInput.value = 'College';
                populateYearLevel(educationData.college.levels);
                populateDepartments();
                yearLevelGroup?.classList.remove('hidden');
                document.getElementById('strand-group')?.classList.add('hidden');
                document.getElementById('college-group')?.classList.remove('hidden');
            }
        }

        function populateYearLevel(levels) {
            const select = document.getElementById('year-level');
            if (!select) return;
            select.innerHTML = '<option value="">Select Year/Grade</option>';
            levels.forEach(l => { const o = document.createElement('option'); o.value = l; o.textContent = l; select.appendChild(o); });
        }

        function populateDepartments() {
            const sel = document.getElementById('department');
            if (!sel) return;
            sel.innerHTML = '<option value="">Select Department</option>';
            Object.keys(collegeDepartments).forEach(dep => { const o = document.createElement('option'); o.value = dep; o.textContent = dep; sel.appendChild(o); });
        }

        function populateCourses() {
            const dep = document.getElementById('department')?.value;
            const sel = document.getElementById('course');
            if (!sel || !dep) return;
            sel.innerHTML = '<option value="">Select Course</option>';
            (collegeDepartments[dep] || []).forEach(c => { const o = document.createElement('option'); o.value = c; o.textContent = c; sel.appendChild(o); });
        }

        // ── Password toggle ──────────────────────────────────────
        function togglePassword(btn) {
            const input = btn.parentElement.querySelector('input');
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.innerHTML = input.type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        }

        // ── Form submit validation ───────────────────────────────
        document.getElementById('register-form')?.addEventListener('submit', function(e) {
            if (!currentRole) {
                e.preventDefault();
                alert("⚠️ Please select a role (Student / Parent / Treasurer / Cashier)");
                document.getElementById('role-buttons')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
            document.getElementById('role-input').value = currentRole;

            if (currentRole === 'student') {
                const age        = document.getElementById('age')?.value;
                const levelGroup = document.getElementById('level_group')?.value;
                if (!age || age < 3) {
                    e.preventDefault();
                    alert("⚠️ Please enter a valid age (3 or above)");
                    document.getElementById('age')?.focus();
                    return false;
                }
                if (!levelGroup) {
                    e.preventDefault();
                    alert("⚠️ Please complete the education level selection.");
                    return false;
                }
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Account…';
                setTimeout(() => { submitBtn.disabled = false; submitBtn.innerHTML = 'Create Account'; }, 10000);
            }
            return true;
        });

        // ── Init ─────────────────────────────────────────────────
        window.addEventListener('DOMContentLoaded', function() {
            @if (session('registration_success'))
                switchTab(0);
            @elseif ($errors->any())
                switchTab(1);
            @else
                switchTab(0);
            @endif
        });
    </script>
</body>
</html>