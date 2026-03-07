{{-- resources/views/students/hs/layouts/hs-app.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') • PAC Student Portal</title>
    @include('partials.favicon')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,700&family=Righteous&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --hs-violet:   #4f46e5;
            --hs-violet-l: #6366f1;
            --hs-pink:     #ec4899;
            --hs-cyan:     #0891b2;
            --hs-amber:    #d97706;
            --hs-emerald:  #059669;
            --hs-rose:     #e11d48;
            --hs-dark:     #f9fafb;
            --hs-dark2:    #ffffff;
            --hs-dark3:    #eef2ff;
            --hs-border:   #e5e7eb;
            --hs-text:     #1f2937;
            --hs-muted:    #6b7280;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-display { font-family: 'Righteous', cursive; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }

        /* Sidebar nav */
        .hs-nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 16px; border-radius: 14px;
            font-weight: 600; font-size: 14px;
            color: #6b7280;
            transition: all 0.2s ease;
            position: relative; overflow: hidden;
            text-decoration: none;
        }
        .hs-nav-link:hover {
            color: #4f46e5;
            background: #eef2ff;
        }
        .hs-nav-link.active {
            background: #eef2ff;
            color: #4f46e5;
            border-left: 3px solid #4f46e5;
            font-weight: 700;
        }
        .hs-nav-link.active::before { display: none; }
        .hs-nav-icon {
            width: 34px; height: 34px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
            background: #f3f4f6;
            transition: all 0.2s ease;
        }
        .hs-nav-link.active .hs-nav-icon {
            background: #c7d2fe;
        }

        /* Card hover */
        .hs-card-lift {
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }
        .hs-card-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(79,70,229,0.12);
        }

        /* Header */
        .hs-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        /* Gradient text */
        .grad-text {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .grad-text-cyan {
            background: linear-gradient(135deg, #0891b2, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animations */
        @keyframes hs-fade-up {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hs-fade { animation: hs-fade-up 0.45s ease both; }
        .hs-d1  { animation-delay: 0.06s; }
        .hs-d2  { animation-delay: 0.12s; }
        .hs-d3  { animation-delay: 0.18s; }
        .hs-d4  { animation-delay: 0.24s; }

        @keyframes hs-float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-6px); }
        }
        .hs-float { animation: hs-float 3.5s ease-in-out infinite; }

        @keyframes hs-shimmer {
            0%   { transform: translateX(-100%) skewX(-12deg); }
            100% { transform: translateX(250%) skewX(-12deg); }
        }
        .hs-shimmer {
            position: relative; overflow: hidden;
        }
        .hs-shimmer::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: hs-shimmer 3s ease-in-out infinite;
        }

        @keyframes hs-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .hs-pulse { animation: hs-pulse 2s ease-in-out infinite; }

        /* XP/progress bar */
        @keyframes hs-bar-fill {
            from { width: 0%; }
        }
        .hs-bar-anim { animation: hs-bar-fill 1.4s cubic-bezier(.4,0,.2,1) both; }

        /* Badges */
        .hs-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.05em;
        }
        .hs-badge-sky    { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .hs-badge-cyan   { background: #cffafe; color: #155e75; border: 1px solid #a5f3fc; }
        .hs-badge-violet { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }
        .hs-badge-green  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .hs-badge-red    { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .hs-badge-amber  { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

        /* Alert banners */
        .hs-alert-success {
            background: #ecfdf5;
            border-left: 4px solid #059669;
            color: #065f46; padding: 14px 18px; border-radius: 12px;
            border: 1px solid #a7f3d0;
        }
        .hs-alert-error {
            background: #fff1f2;
            border-left: 4px solid #e11d48;
            color: #9f1239; padding: 14px 18px; border-radius: 12px;
            border: 1px solid #fecdd3;
        }

        /* Scrollbar */
        .hs-scroll::-webkit-scrollbar { width: 5px; }
        .hs-scroll::-webkit-scrollbar-track { background: transparent; }
        .hs-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

        /* Mobile nav backdrop */
        .hs-backdrop { background: rgba(17,24,39,0.5); backdrop-filter: blur(6px); }

        /* Main content area */
        .hs-main {
            background: #f9fafb;
            min-height: 100vh;
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen antialiased flex flex-col" style="background: #f9fafb;"
      x-data="{ mobileMenuOpen: false }"
      @keydown.escape="mobileMenuOpen = false">

    @php
        $user = auth()->user();
        $isJHS = $user && str_contains(strtolower($user->level_group ?? ''), 'junior');
        $isSHS = $user && str_contains(strtolower($user->level_group ?? ''), 'senior');
        $levelLabel = $isJHS ? 'Junior High' : ($isSHS ? 'Senior High' : 'High School');
        $accentColor = $isJHS ? '#06b6d4' : '#06b6d4';

        $nav = [
            ['route' => 'hs.dashboard',    'label' => 'Dashboard',    'icon' => '🏠', 'desc' => 'My Overview'],
            ['route' => 'hs.billing',       'label' => 'Billing',      'icon' => '₱',  'desc' => 'Fees & Charges'],
            ['route' => 'hs.installments',  'label' => 'Installments', 'icon' => '📅', 'desc' => 'Payment Plans'],
            ['route' => 'hs.statements',    'label' => 'Statements',   'icon' => '📋', 'desc' => 'Payment History'],
            ['route' => 'hs.profile',       'label' => 'Profile',      'icon' => '👤', 'desc' => 'My Account'],
        ];
    @endphp

    <!-- ═══════════════ TOP HEADER ═══════════════ -->
    <header class="hs-header sticky top-0 z-50">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[52px]">

                <!-- Left: hamburger + branding -->
                <div class="flex items-center gap-3">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="sm:hidden w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-base"
                             style="background: linear-gradient(135deg, #0ea5e9, #06b6d4);">
                            🎓
                        </div>
                        <div class="hidden sm:block">
                            <p class="font-display text-gray-800 text-base leading-none tracking-wide">PAC Portal</p>
                            <p class="text-[10px] font-semibold mt-0.5" style="color: {{ $accentColor }};">{{ $levelLabel }} School</p>
                        </div>
                    </div>
                </div>

                <!-- Right: user + logout -->
                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-2.5 px-3.5 py-2 rounded-xl"
                         style="background: #f9fafb; border: 1px solid #e5e7eb;">
                        @if(filled(auth()->user()->profile_picture) && Storage::disk('public')->exists(auth()->user()->profile_picture))
                            <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                                 class="w-5 h-5 rounded-full object-cover ring-1"
                                 style="ring-color: {{ $accentColor }};">
                        @else
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white"
                                 style="background: linear-gradient(135deg, #0ea5e9, #06b6d4);">
                                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-gray-800 text-sm font-semibold leading-tight">{{ explode(' ', auth()->user()->name ?? 'Student')[0] }}</p>
                            <p class="text-[10px]" style="color: {{ $accentColor }};">{{ $levelLabel }}</p>
                        </div>
                    </div>

                    <!-- Notification bell -->
                    <button class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>

                    <a href="{{ route('logout') }}"
                       @click.prevent="if(confirm('Log out?')) { document.getElementById('hs-logout-form').submit() }"
                       class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </a>
                    <form id="hs-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </div>
        </div>
    </header>

    <!-- ═══════════════ MOBILE MENU ═══════════════ -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="sm:hidden fixed inset-0 z-40 hs-backdrop"
         @click="mobileMenuOpen = false">
    </div>

    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-220"
         x-transition:enter-start="opacity-0 -translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-180"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 -translate-x-full"
         class="sm:hidden fixed top-[52px] left-0 bottom-0 z-50 w-72 overflow-y-auto hs-scroll shadow-2xl"
         style="background: #ffffff; border-right: 1px solid #e5e7eb;">

        <div class="p-4 border-b border-gray-100 flex items-center justify-between" style="background: #f9fafb;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold text-white shadow-xl"
                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-800 font-bold text-base">{{ auth()->user()->name ?? 'Student' }}</p>
                    <p class="text-xs font-semibold text-indigo-600">{{ $levelLabel }} School</p>
                </div>
            </div>
            <button @click="mobileMenuOpen = false"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all flex-shrink-0"
                    aria-label="Close menu">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="p-4 space-y-1">
            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}"
                   @click="mobileMenuOpen = false"
                   class="hs-nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                    <span class="hs-nav-icon">{{ $item['icon'] }}</span>
                    <div>
                        <p class="text-sm font-bold leading-tight">{{ $item['label'] }}</p>
                        <p class="text-xs opacity-40 mt-0.5">{{ $item['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </nav>

        <div class="p-5 mx-4 mb-4 rounded-2xl" style="background: #eef2ff; border: 1px solid #c7d2fe;">
            <p class="text-xs font-bold text-indigo-500 mb-1">School Year</p>
            <p class="text-indigo-800 font-bold text-sm">{{ date('Y') . '-' . (date('Y')+1) }}</p>
            @if($isSHS)
            <p class="text-xs text-indigo-500 mt-1">Semester available</p>
            @else
            <p class="text-xs text-indigo-500 mt-1">Full year billing</p>
            @endif
        </div>
    </div>

    <!-- ═══════════════ MAIN LAYOUT ═══════════════ -->
    <div class="flex flex-1 overflow-hidden">

        <!-- Desktop Sidebar -->
        <aside class="hidden sm:flex flex-col w-64 lg:w-72 overflow-y-auto hs-scroll flex-shrink-0 shadow-2xl"
               style="background: #ffffff; border-right: 1px solid #e5e7eb;">

            <!-- Student card -->
            <div class="m-4 p-4 rounded-2xl hs-shimmer"
                 style="background: #eef2ff; border: 1px solid #c7d2fe;">
                <div class="flex items-center gap-3">
                    @if(filled(auth()->user()->profile_picture) && Storage::disk('public')->exists(auth()->user()->profile_picture))
                        <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                             class="w-6 h-6 rounded-lg object-cover ring-1 ring-sky-400/50 flex-shrink-0">
                    @else
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0 shadow-lg"
                             style="background: linear-gradient(135deg, #0ea5e9, #06b6d4);">
                            {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-gray-800 font-bold text-sm leading-tight truncate">{{ auth()->user()->name ?? 'Student' }}</p>
                        <p class="text-xs font-semibold mt-0.5 text-indigo-600">{{ auth()->user()->student_id ?? $levelLabel }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3" style="border-top: 1px solid #e0e7ff;">
                    <p class="text-xs text-gray-400">Year Level</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">{{ auth()->user()->year_level ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Nav links -->
            <nav class="flex-1 px-4 pb-4 space-y-0.5">
                @foreach($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="hs-nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                        <span class="hs-nav-icon">{{ $item['icon'] }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold leading-tight">{{ $item['label'] }}</p>
                            <p class="text-xs opacity-35 mt-0.5">{{ $item['desc'] }}</p>
                        </div>
                        @if(request()->routeIs($item['route'].'*'))
                            <svg class="w-4 h-4 ml-auto opacity-50 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    </a>
                @endforeach
            </nav>

            <!-- School year tag -->
            <div class="m-4 p-4 rounded-2xl" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full hs-pulse" style="background: {{ $accentColor }};"></span>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Current Period</p>
                </div>
                <p class="font-bold text-gray-800 text-sm">S.Y. {{ date('Y') . '–' . (date('Y')+1) }}</p>
                @if($isSHS)
                    <p class="text-xs mt-1 text-indigo-500">Senior High — Semester Based</p>
                @else
                    <p class="text-xs mt-1 text-indigo-500">Junior High — Annual Billing</p>
                @endif
            </div>

            <div class="px-4 py-3 text-center" style="border-top: 1px solid #f3f4f6;">
                <p class="text-gray-300 text-xs">PAC © {{ date('Y') }}</p>
            </div>
        </aside>

        <!-- ═══════════ MAIN CONTENT ═══════════ -->
        <main class="flex-1 overflow-y-auto hs-main hs-scroll">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-6">

                @if($errors->any())
                    <div class="hs-alert-error hs-fade flex gap-3 items-start">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" style="color: #e11d48;">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            @foreach($errors->all() as $error)
                                <p class="text-sm font-medium">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="hs-alert-success hs-fade flex gap-3 items-center" x-data="{show:true}" x-show="show">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="color: #34d399;">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1">{{ session('success') }}</p>
                        <button @click="show=false" class="text-emerald-600 hover:text-emerald-800 text-xl leading-none font-bold">&times;</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs" style="background: #ffffff; border-top: 1px solid #f3f4f6; color: #d1d5db;">
        © {{ date('Y') }} PAC &nbsp;·&nbsp; {{ $levelLabel }} School Payment Portal
    </footer>

    @stack('scripts')
</body>
</html>