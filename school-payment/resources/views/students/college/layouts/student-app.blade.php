<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') • PAC Online Payment</title>
    @include('partials.favicon')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --surface: #f9fafb;
            --white:   #ffffff;
            --indigo:  #4f46e5;
            --indigo-l:#eef2ff;
            --emerald: #059669;
            --rose:    #e11d48;
            --amber:   #d97706;
            --text:    #1f2937;
            --muted:   #6b7280;
            --border:  #e5e7eb;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Sora', sans-serif; background-color: #f9fafb; color: #1f2937; }
        .font-display  { font-family: 'DM Serif Display', serif; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }

        /* ── Sidebar nav links ── */
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: 12px;
            font-weight: 600; font-size: 14px;
            color: #6b7280;
            transition: all 0.2s ease;
            position: relative; overflow: hidden;
            text-decoration: none;
        }
        .nav-link:hover {
            color: #1f2937;
            background: #f3f4f6;
        }
        .nav-link.active {
            background: #eef2ff;
            color: #4f46e5;
            border-left: 3px solid #4f46e5;
            font-weight: 700;
        }
        .nav-icon {
            width: 32px; height: 32px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
            background: #f3f4f6;
            transition: all 0.2s ease;
        }
        .nav-link.active .nav-icon {
            background: #e0e7ff;
        }

        /* ── Header ── */
        .col-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        /* ── Main content background ── */
        .col-main {
            background: #f9fafb;
            min-height: 100vh;
        }

        /* ── Card hover lift ── */
        .card-lift {
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }
        .card-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(79,70,229,0.10);
        }

        /* ── Gradient text ── */
        .grad-text {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Animations ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up    { animation: fadeInUp 0.45s ease both; }
        .fade-up-d1 { animation-delay: 0.06s; }
        .fade-up-d2 { animation-delay: 0.12s; }
        .fade-up-d3 { animation-delay: 0.18s; }
        .fade-up-d4 { animation-delay: 0.24s; }

        @keyframes col-float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-6px); }
        }
        .col-float { animation: col-float 3.5s ease-in-out infinite; }

        @keyframes col-shimmer {
            0%   { transform: translateX(-100%) skewX(-12deg); }
            100% { transform: translateX(250%) skewX(-12deg); }
        }
        .col-shimmer {
            position: relative; overflow: hidden;
        }
        .col-shimmer::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
            animation: col-shimmer 3s ease-in-out infinite;
        }

        @keyframes col-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .col-pulse { animation: col-pulse 2s ease-in-out infinite; }

        @keyframes col-bar-fill {
            from { width: 0%; }
        }
        .col-bar-anim { animation: col-bar-fill 1.4s cubic-bezier(.4,0,.2,1) both; }

        /* ── Badges ── */
        .col-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.05em;
        }
        .col-badge-indigo { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
        .col-badge-green  { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .col-badge-red    { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
        .col-badge-amber  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .col-badge-cyan   { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
        .col-badge-teal   { background: #f0fdfa; color: #0d9488; border: 1px solid #99f6e4; }
        .col-badge-violet { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }

        /* ── Alert banners ── */
        .alert-success {
            background: #ecfdf5;
            border-left: 4px solid #059669;
            color: #065f46; padding: 14px 18px; border-radius: 12px;
            border: 1px solid #a7f3d0;
        }
        .alert-error {
            background: #fff1f2;
            border-left: 4px solid #e11d48;
            color: #9f1239; padding: 14px 18px; border-radius: 12px;
            border: 1px solid #fecdd3;
        }

        /* ── Scrollbar ── */
        .styled-scroll::-webkit-scrollbar { width: 5px; }
        .styled-scroll::-webkit-scrollbar-track { background: transparent; }
        .styled-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

        /* ── Mobile nav backdrop ── */
        .mobile-nav-backdrop { background: rgba(0,0,0,0.35); backdrop-filter: blur(4px); }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen antialiased flex flex-col"
      x-data="{ mobileMenuOpen: false }"
      @keydown.escape="mobileMenuOpen = false">

    @php
        $user = auth()->user();
        $nav = [
            ['route' => 'student.dashboard',   'label' => 'Dashboard',    'icon' => '🏠', 'desc' => 'My Overview'],
            ['route' => 'student.billing',      'label' => 'Billing',      'icon' => '₱',  'desc' => 'Fees & Charges'],
            ['route' => 'student.installments', 'label' => 'Installments', 'icon' => '📅', 'desc' => 'Payment Plans'],
            ['route' => 'student.statements',   'label' => 'Statements',   'icon' => '📋', 'desc' => 'Payment History'],
            ['route' => 'student.profile',      'label' => 'Profile',      'icon' => '👤', 'desc' => 'My Account'],
        ];
    @endphp

    <!-- ═══════════════════════ TOP HEADER ═══════════════════════ -->
    <header class="col-header sticky top-0 z-50 shadow-sm">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[56px]">

                <!-- Left: hamburger + branding -->
                <div class="flex items-center gap-3">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="sm:hidden w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen"  stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-base shadow-sm"
                             style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                            🎓
                        </div>
                        <div class="hidden sm:block">
                            <p class="font-bold text-gray-800 text-base leading-none tracking-tight">PAC Payment</p>
                            <p class="text-[10px] font-semibold mt-0.5 text-indigo-500">College Portal</p>
                        </div>
                    </div>
                </div>

                <!-- Right: user pill + notification + logout -->
                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200">
                        @if(filled($user->profile_picture) && Storage::disk('public')->exists($user->profile_picture))
                            <img src="{{ Storage::url($user->profile_picture) }}"
                                 class="w-5 h-5 rounded-full object-cover ring-1 ring-indigo-200 flex-shrink-0">
                        @else
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                                 style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                                {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-gray-800 text-sm font-semibold leading-tight">{{ explode(' ', $user->name ?? 'Student')[0] }}</p>
                            <p class="text-[10px] text-indigo-500 font-semibold">{{ strtoupper($user->program ?? 'College') }}</p>
                        </div>
                    </div>

                    <!-- Notification bell -->
                    <button class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>

                    <a href="{{ route('logout') }}"
                       @click.prevent="if(confirm('Log out of your account?')) { document.getElementById('logout-form').submit() }"
                       class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>

            </div>
        </div>
    </header>

    <!-- ═══════════════════ MOBILE MENU OVERLAY ═══════════════════ -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="sm:hidden fixed inset-0 z-40 mobile-nav-backdrop"
         @click="mobileMenuOpen = false">
    </div>

    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-220"
         x-transition:enter-start="opacity-0 -translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-180"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 -translate-x-full"
         class="sm:hidden fixed top-[56px] left-0 bottom-0 z-50 w-72 overflow-y-auto styled-scroll shadow-xl bg-white border-r border-gray-200">

        <!-- Mobile user info -->
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if(filled($user->profile_picture) && Storage::disk('public')->exists($user->profile_picture))
                    <img src="{{ Storage::url($user->profile_picture) }}"
                         class="w-10 h-10 rounded-full object-cover ring-2 ring-indigo-200 flex-shrink-0">
                @else
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                         style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <p class="text-gray-800 font-bold text-base">{{ $user->name ?? 'Student' }}</p>
                    <p class="text-xs font-semibold text-indigo-500">College Student</p>
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

        <!-- Mobile nav -->
        <nav class="p-4 space-y-1">
            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}"
                   @click="mobileMenuOpen = false"
                   class="nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                    <span class="nav-icon">{{ $item['icon'] }}</span>
                    <div>
                        <p class="text-sm font-bold leading-tight">{{ $item['label'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </nav>

        <!-- Mobile school year widget -->
        <div class="p-4 mx-4 mb-4 rounded-2xl bg-indigo-50 border border-indigo-100">
            <p class="text-xs font-bold text-indigo-500 mb-1">School Year</p>
            <p class="text-gray-800 font-bold text-sm">{{ date('Y') . '–' . (date('Y')+1) }}</p>
            <p class="text-xs text-indigo-400 mt-1">College — Annual Billing</p>
        </div>
    </div>

    <!-- ═══════════════════════ MAIN LAYOUT ═══════════════════════ -->
    <div class="flex flex-1 overflow-hidden">

        <!-- Desktop Sidebar -->
        <aside class="hidden sm:flex flex-col w-64 lg:w-72 overflow-y-auto styled-scroll flex-shrink-0 bg-white border-r border-gray-200 shadow-sm">

            <!-- Student card -->
            <div class="m-4 p-4 rounded-2xl bg-indigo-50 border border-indigo-100">
                <div class="flex items-center gap-3">
                    @if(filled($user->profile_picture) && Storage::disk('public')->exists($user->profile_picture))
                        <img src="{{ Storage::url($user->profile_picture) }}"
                             class="w-10 h-10 rounded-full object-cover ring-2 ring-indigo-200 flex-shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                             style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                            {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-gray-800 font-bold text-sm leading-tight truncate">{{ $user->name ?? 'Student' }}</p>
                        <p class="text-xs font-semibold mt-0.5 text-indigo-500">{{ $user->student_id ?? 'College Portal' }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-indigo-100">
                    <p class="text-xs text-gray-400">Year Level</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $user->year_level ? $user->year_level . ' Year' : 'N/A' }}</p>
                </div>
            </div>

            <!-- Nav links -->
            <nav class="flex-1 px-3 pb-4 space-y-0.5">
                @foreach($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                        <span class="nav-icon">{{ $item['icon'] }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold leading-tight">{{ $item['label'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                        </div>
                        @if(request()->routeIs($item['route'].'*'))
                            <svg class="w-4 h-4 ml-auto text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    </a>
                @endforeach
            </nav>

            <!-- School year / period widget -->
            <div class="m-4 p-4 rounded-2xl bg-gray-50 border border-gray-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full col-pulse bg-indigo-400"></span>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Current Period</p>
                </div>
                <p class="font-bold text-gray-800 text-sm">S.Y. {{ date('Y') . '–' . (date('Y')+1) }}</p>
                <p class="text-xs mt-1 text-indigo-500">College — Annual Billing</p>
            </div>

            <div class="px-4 py-3 text-center border-t border-gray-100">
                <p class="text-gray-300 text-xs">PAC © {{ date('Y') }}</p>
            </div>
        </aside>

        <!-- ═══════════════════ MAIN CONTENT ═══════════════════ -->
        <main class="flex-1 overflow-y-auto col-main styled-scroll">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-6">

                @if($errors->any())
                    <div class="alert-error fade-up flex gap-3 items-start">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
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
                    <div class="alert-success fade-up flex gap-3 items-center" x-data="{show:true}" x-show="show">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
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
    <footer class="py-4 text-center text-xs text-gray-300 bg-white border-t border-gray-100">
        © {{ date('Y') }} PAC &nbsp;·&nbsp; College Student Payment Portal
    </footer>

    @stack('scripts')

</body>
</html>