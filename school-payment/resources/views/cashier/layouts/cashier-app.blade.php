{{-- resources/views/cashier/layouts/cashier-app.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') • PAC Cashier Portal</title>
    @include('partials.favicon')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,700&family=Righteous&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --c-primary:   #4f46e5;
            --c-primary-l: #6366f1;
            --c-bg:        #f9fafb;
            --c-surface:   #ffffff;
            --c-border:    #e5e7eb;
            --c-text:      #1f2937;
            --c-muted:     #9ca3af;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-display { font-family: 'Righteous', cursive; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }

        /* ── Sidebar nav ── */
        .c-nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: 14px;
            font-weight: 600; font-size: 13.5px;
            color: #6b7280;
            transition: all 0.2s ease;
            position: relative; overflow: hidden;
            text-decoration: none;
        }
        .c-nav-link:hover { color: #1f2937; background: #f3f4f6; }
        .c-nav-link.active {
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }
        .c-nav-link.active::before {
            content: '';
            position: absolute; left: 0; top: 25%; bottom: 25%;
            width: 3px;
            background: linear-gradient(180deg, #4f46e5, #6366f1);
            border-radius: 0 3px 3px 0;
        }
        .c-nav-icon {
            width: 32px; height: 32px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
            background: #f3f4f6;
            transition: all 0.2s ease;
        }
        .c-nav-link.active .c-nav-icon {
            background: #e0e7ff;
        }

        /* ── Header ── */
        .c-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        /* ── Main ── */
        .c-main {
            background: #f9fafb;
            min-height: 100vh;
        }

        /* ── Gradient text ── */
        .grad-text {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Cards ── */
        .c-card-lift { transition: transform 0.22s ease, box-shadow 0.22s ease; }
        .c-card-lift:hover { transform: translateY(-4px); box-shadow: 0 20px 50px rgba(79,70,229,0.08); }

        /* ── Badges ── */
        .c-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.05em;
        }
        .c-badge-green  { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .c-badge-red    { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
        .c-badge-amber  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .c-badge-cyan   { background: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc; }
        .c-badge-sky    { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
        .c-badge-violet { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }
        .c-badge-gray   { background: #f9fafb; color: #6b7280; border: 1px solid #e5e7eb; }

        /* ── Alerts ── */
        .c-alert-success { background: #f0fdf4; border-left: 4px solid #22c55e; color: #15803d; padding: 14px 18px; border-radius: 12px; }
        .c-alert-error   { background: #fff1f2; border-left: 4px solid #f43f5e; color: #be123c; padding: 14px 18px; border-radius: 12px; }

        /* ── Inputs ── */
        .c-input {
            width: 100%;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 10px 14px;
            color: #1f2937;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s ease;
        }
        .c-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .c-input::placeholder { color: #9ca3af; }
        .c-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234f46e5' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 40px !important; }

        /* ── Animations ── */
        @keyframes c-fade-up {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .c-fade { animation: c-fade-up 0.45s ease both; }
        .c-d1  { animation-delay: 0.06s; }
        .c-d2  { animation-delay: 0.12s; }
        .c-d3  { animation-delay: 0.18s; }
        .c-d4  { animation-delay: 0.24s; }

        @keyframes c-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .c-pulse { animation: c-pulse 2s ease-in-out infinite; }

        /* ── Table rows ── */
        .c-row { transition: background 0.15s ease; cursor: default; }
        .c-row:hover { background: #f5f3ff !important; }

        /* ── Scrollbar ── */
        .c-scroll::-webkit-scrollbar { width: 5px; }
        .c-scroll::-webkit-scrollbar-track { background: transparent; }
        .c-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

        /* ── Backdrop ── */
        .c-backdrop { background: rgba(0,0,0,0.25); backdrop-filter: blur(4px); }

        /* ── Modal ── */
        .c-modal {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }

        /* ── Progress ── */
        .c-progress-track { background: #f3f4f6; border-radius: 999px; height: 6px; }
        .c-progress-fill  { background: linear-gradient(90deg, #4f46e5, #6366f1); border-radius: 999px; height: 6px; transition: width 0.5s ease; }

        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen antialiased flex flex-col bg-gray-50"
      x-data="{ mobileMenuOpen: false }"
      @keydown.escape="mobileMenuOpen = false">

    <!-- ═══════════════ TOP HEADER ═══════════════ -->
    <header class="c-header sticky top-0 z-50 no-print">
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
                             style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                            💵
                        </div>
                        <div class="hidden sm:block">
                            <p class="font-display text-gray-800 text-base leading-none tracking-wide">PAC Cashier</p>
                            <p class="text-[10px] font-semibold mt-0.5 text-indigo-500">Payment Management</p>
                        </div>
                    </div>
                </div>

                <!-- Right: user + logout -->
                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-100">
                        @if(filled(auth()->user()->profile_picture) && Storage::disk('public')->exists(auth()->user()->profile_picture))
                            <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                                 class="w-5 h-5 rounded-full object-cover ring-1 ring-indigo-300">
                        @else
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white"
                                 style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                                {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-gray-800 text-sm font-semibold leading-tight">{{ explode(' ', auth()->user()->name ?? 'Cashier')[0] }}</p>
                            <p class="text-[10px] text-indigo-500">Cashier</p>
                        </div>
                    </div>

                    <a href="{{ route('logout') }}"
                       @click.prevent="if(confirm('Log out?')) { document.getElementById('c-logout-form').submit() }"
                       class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </a>
                    <form id="c-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </div>
        </div>
    </header>

    <!-- ═══════════════ MOBILE BACKDROP ═══════════════ -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="sm:hidden fixed inset-0 z-40 bg-black/30 backdrop-blur-sm"
         @click="mobileMenuOpen = false">
    </div>

    <!-- ═══════════════ MOBILE SIDEBAR ═══════════════ -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-220"
         x-transition:enter-start="opacity-0 -translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-180"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 -translate-x-full"
         class="sm:hidden fixed top-[52px] left-0 bottom-0 z-50 w-72 flex flex-col no-print bg-white border-r border-gray-100 shadow-xl">

        {{-- Close button row --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-800 font-bold text-sm">{{ auth()->user()->name ?? 'Cashier' }}</p>
                    <p class="text-xs font-semibold text-indigo-500">Cashier Staff</p>
                </div>
            </div>
            <button @click="mobileMenuOpen=false"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto c-scroll p-4 space-y-1">
            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}"
                   @click="mobileMenuOpen = false"
                   class="c-nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                    <span class="c-nav-icon">{{ $item['icon'] }}</span>
                    <div>
                        <p class="text-sm font-bold leading-tight">{{ $item['label'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                    </div>
                </a>
            @endforeach

            {{-- Profile link --}}
            <a href="{{ route('cashier.profile') }}"
               @click="mobileMenuOpen = false"
               class="c-nav-link {{ request()->routeIs('cashier.profile*') ? 'active' : '' }}">
                <span class="c-nav-icon">👤</span>
                <div>
                    <p class="text-sm font-bold leading-tight">My Profile</p>
                    <p class="text-xs text-gray-400 mt-0.5">Contact &amp; address</p>
                </div>
            </a>
        </nav>
    </div>

    <!-- ═══════════════ MAIN LAYOUT ═══════════════ -->
    <div class="flex flex-1 overflow-hidden">

        <!-- Desktop Sidebar -->
        <aside class="hidden sm:flex flex-col w-60 lg:w-64 overflow-y-auto c-scroll flex-shrink-0 no-print bg-white border-r border-gray-100 shadow-sm">

            <!-- Cashier card -->
            <a href="{{ route('cashier.profile') }}"
               class="m-4 p-4 rounded-2xl bg-indigo-50 border border-indigo-100 block transition-all hover:bg-indigo-100 hover:border-indigo-200 group">
                <div class="flex items-center gap-3">
                    @if(filled(auth()->user()->profile_picture) && Storage::disk('public')->exists(auth()->user()->profile_picture))
                        <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                             class="w-9 h-9 rounded-lg object-cover ring-2 ring-indigo-200 flex-shrink-0">
                    @else
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                             style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                            {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-800 font-bold text-sm leading-tight truncate">{{ auth()->user()->name ?? 'Cashier' }}</p>
                        <p class="text-xs font-semibold mt-0.5 text-indigo-500">
                            {{ auth()->user()->position ?? 'Cashier Staff' }}
                        </p>
                    </div>
                    <svg class="w-3.5 h-3.5 text-indigo-300 group-hover:text-indigo-500 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <div class="mt-3 pt-3 border-t border-indigo-100 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full c-pulse bg-indigo-400"></span>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Active Session</p>
                    </div>
                    <p class="text-[10px] font-semibold text-indigo-400 group-hover:text-indigo-600 transition-colors">View Profile →</p>
                </div>
            </a>

            <!-- Nav links -->
            <nav class="flex-1 px-3 pb-4 space-y-0.5">
                @foreach($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="c-nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                        <span class="c-nav-icon">{{ $item['icon'] }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold leading-tight">{{ $item['label'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                        </div>
                        @if(request()->routeIs($item['route'].'*'))
                            <svg class="w-3.5 h-3.5 ml-auto text-indigo-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    </a>
                @endforeach

                {{-- Profile link --}}
                <a href="{{ route('cashier.profile') }}"
                   class="c-nav-link {{ request()->routeIs('cashier.profile*') ? 'active' : '' }}">
                    <span class="c-nav-icon">👤</span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold leading-tight">My Profile</p>
                        <p class="text-xs text-gray-400 mt-0.5">Contact &amp; address</p>
                    </div>
                    @if(request()->routeIs('cashier.profile*'))
                        <svg class="w-3.5 h-3.5 ml-auto text-indigo-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif
                </a>
            </nav>

            <!-- School year -->
            <div class="m-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-1.5 h-1.5 rounded-full c-pulse bg-indigo-400"></span>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Current Period</p>
                </div>
                <p class="font-bold text-gray-800 text-sm">S.Y. {{ date('Y') . '–' . (date('Y')+1) }}</p>
                <p class="text-xs mt-0.5 text-indigo-500">All Level Groups</p>
            </div>

            <div class="px-4 py-3 text-center border-t border-gray-100">
                <p class="text-gray-300 text-xs">PAC © {{ date('Y') }}</p>
            </div>
        </aside>

        <!-- ═══════════ MAIN CONTENT ═══════════ -->
        <main class="flex-1 overflow-y-auto c-main c-scroll">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7 space-y-6">

                @if($errors->any())
                    <div class="c-alert-error c-fade flex gap-3 items-start">
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
                    <div class="c-alert-success c-fade flex gap-3 items-center" x-data="{show:true}" x-show="show">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1">{{ session('success') }}</p>
                        <button @click="show=false" class="text-emerald-500 hover:text-emerald-700 text-xl leading-none font-bold">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="c-alert-error c-fade flex gap-3 items-center" x-data="{show:true}" x-show="show">
                        <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1">{{ session('error') }}</p>
                        <button @click="show=false" class="text-red-400 hover:text-red-600 text-xl leading-none font-bold">&times;</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <footer class="py-3 text-center text-xs no-print bg-white border-t border-gray-100 text-gray-300">
        © {{ date('Y') }} PAC &nbsp;·&nbsp; Cashier Portal
    </footer>

    @stack('scripts')
</body>
</html>