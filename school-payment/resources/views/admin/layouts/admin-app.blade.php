{{-- resources/views/admin/layouts/admin-app.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') • PAC Admin Portal</title>
    @include('partials.favicon')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --a-bg:      #f9fafb;
            --a-bg2:     #ffffff;
            --a-bg3:     #f3f4f6;
            --a-indigo:  #4f46e5;
            --a-indigo2: #6366f1;
            --a-rose:    #e11d48;
            --a-amber:   #d97706;
            --a-emerald: #059669;
            --a-sky:     #0ea5e9;
            --a-border:  #e5e7eb;
            --a-text:    #1f2937;
            --a-muted:   #6b7280;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--a-bg); color: var(--a-text); }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }

        /* ── Sidebar nav ── */
        .a-nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 13px; border-radius: 14px;
            font-weight: 600; font-size: 13px;
            color: #6b7280;
            transition: all 0.2s ease;
            position: relative; overflow: hidden;
            text-decoration: none;
        }
        .a-nav-link:hover { color: #1f2937; background: #f3f4f6; }
        .a-nav-link.active {
            background: #eef2ff;
            color: #4f46e5;
            border: 1px solid #c7d2fe;
            box-shadow: 0 2px 8px rgba(79,70,229,0.1);
        }
        .a-nav-link.active::before {
            content: '';
            position: absolute; left: 0; top: 25%; bottom: 25%;
            width: 3px;
            background: linear-gradient(180deg, #4f46e5, #6366f1);
            border-radius: 0 3px 3px 0;
        }
        .a-nav-icon {
            width: 30px; height: 30px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
            background: #f3f4f6;
            transition: all 0.2s ease;
        }
        .a-nav-link.active .a-nav-icon {
            background: #e0e7ff;
        }

        /* ── Header ── */
        .a-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        /* ── Main bg ── */
        .a-main {
            background: var(--a-bg);
            min-height: 100vh;
        }

        /* ── Stat cards ── */
        .a-stat-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 20px 22px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .a-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(79,70,229,0.1); }

        /* ── Table ── */
        .a-table { width: 100%; border-collapse: collapse; }
        .a-table th {
            text-align: left; padding: 11px 14px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.07em;
            text-transform: uppercase; color: #9ca3af;
            border-bottom: 1px solid #f3f4f6;
            white-space: nowrap;
            background: #f9fafb;
        }
        .a-table td { padding: 13px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13.5px; color: #374151; }
        .a-table tr:last-child td { border-bottom: none; }
        .a-table tr:hover td { background: #f9fafb; }

        /* ── Badges ── */
        .a-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.04em;
        }
        .a-badge-violet  { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
        .a-badge-emerald { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .a-badge-red     { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
        .a-badge-amber   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .a-badge-sky     { background: #f0f9ff; color: #0ea5e9; border: 1px solid #bae6fd; }
        .a-badge-gray    { background: #f9fafb; color: #6b7280; border: 1px solid #e5e7eb; }

        /* ── Inputs ── */
        .a-input {
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
        .a-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .a-input::placeholder { color: #9ca3af; }
        .a-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px !important;
        }

        /* ── Buttons ── */
        .a-btn-primary {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: #fff; border: none;
            padding: 10px 20px; border-radius: 12px;
            font-weight: 700; font-size: 13.5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .a-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(79,70,229,0.3); }
        .a-btn-secondary {
            background: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 9px 18px; border-radius: 12px;
            font-weight: 600; font-size: 13px;
            cursor: pointer; transition: all 0.2s ease;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .a-btn-secondary:hover { background: #f9fafb; border-color: #9ca3af; color: #111827; }
        .a-btn-danger {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #fecdd3;
            padding: 8px 16px; border-radius: 10px;
            font-weight: 600; font-size: 12.5px;
            cursor: pointer; transition: all 0.2s ease;
        }
        .a-btn-danger:hover { background: #ffe4e6; }

        /* ── Alerts ── */
        .a-alert-success { background: #ecfdf5; border-left: 4px solid #059669; color: #065f46; padding: 14px 18px; border-radius: 12px; }
        .a-alert-error   { background: #fff1f2; border-left: 4px solid #e11d48; color: #9f1239; padding: 14px 18px; border-radius: 12px; }
        .a-alert-warning { background: #fffbeb; border-left: 4px solid #d97706; color: #92400e; padding: 14px 18px; border-radius: 12px; }

        /* ── Section cards ── */
        .a-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        /* ── Animations ── */
        @keyframes a-fade-up {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .a-fade  { animation: a-fade-up 0.4s ease both; }
        .a-d1 { animation-delay: 0.05s; }
        .a-d2 { animation-delay: 0.10s; }
        .a-d3 { animation-delay: 0.15s; }
        .a-d4 { animation-delay: 0.20s; }
        .a-d5 { animation-delay: 0.25s; }

        @keyframes a-pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .a-pulse { animation: a-pulse 2s ease-in-out infinite; }

        /* ── Scrollbar ── */
        .a-scroll::-webkit-scrollbar { width: 5px; }
        .a-scroll::-webkit-scrollbar-track { background: transparent; }
        .a-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

        /* ── Progress bar ── */
        .a-progress-track { background: #f3f4f6; border-radius: 999px; height: 6px; }
        .a-progress-fill  { background: linear-gradient(90deg, #4f46e5, #6366f1); border-radius: 999px; height: 6px; transition: width 0.5s ease; }

        /* ── Print ── */
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

    {{-- ═══ TOP HEADER ═══ --}}
    <header class="a-header sticky top-0 z-50 no-print">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[52px]">

                {{-- Left: hamburger + branding --}}
                <div class="flex items-center gap-3">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="sm:hidden w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-base"
                             style="background: linear-gradient(135deg, #4f46e5, #4338ca); box-shadow: 0 4px 12px rgba(79,70,229,0.3);">
                            🛡️
                        </div>
                        <div class="hidden sm:block">
                            <p class="font-bold text-gray-800 text-base leading-none">PAC Admin</p>
                            <p class="text-[10px] font-semibold mt-0.5 text-indigo-600">System Administrator</p>
                        </div>
                    </div>
                </div>

                {{-- Right: school year + user --}}
                <div class="flex items-center gap-2">
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200">
                        <span class="w-1.5 h-1.5 rounded-full a-pulse bg-indigo-500"></span>
                        <span class="text-xs font-semibold text-indigo-600">SY {{ date('Y').'-'.(date('Y')+1) }}</span>
                    </div>

                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200">
                        @if(filled(auth()->user()->profile_picture) && Storage::disk('public')->exists(auth()->user()->profile_picture))
                            <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                                 class="w-5 h-5 rounded-full object-cover ring-1 ring-indigo-400">
                        @else
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white"
                                 style="background: linear-gradient(135deg, #4f46e5, #4338ca);">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-gray-800 text-sm font-semibold leading-tight">{{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}</p>
                            <p class="text-[10px] text-indigo-600">Admin</p>
                        </div>
                    </div>

                    <a href="{{ route('logout') }}"
                       @click.prevent="if(confirm('Log out?')) { document.getElementById('a-logout-form').submit() }"
                       class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </a>
                    <form id="a-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══ MOBILE BACKDROP ═══ --}}
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="sm:hidden fixed inset-0 z-40 bg-black/30 backdrop-blur-sm" @click="mobileMenuOpen=false"></div>

    {{-- ═══ MOBILE SIDEBAR ═══ --}}
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-220" x-transition:enter-start="opacity-0 -translate-x-full" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-180" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-full"
         class="sm:hidden fixed top-[52px] left-0 bottom-0 z-50 w-72 flex flex-col no-print bg-white border-r border-gray-100 shadow-xl">

        {{-- Close button row --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 flex-shrink-0">
            <p class="text-sm font-bold text-gray-700">Navigation</p>
            <button @click="mobileMenuOpen=false"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Nav links --}}
        <nav class="flex-1 overflow-y-auto a-scroll p-4 space-y-0.5">
            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}" @click="mobileMenuOpen=false"
                   class="a-nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                    <span class="a-nav-icon">{{ $item['icon'] }}</span>
                    <div>
                        <p class="text-sm font-bold leading-tight">{{ $item['label'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </nav>
    </div>

    {{-- ═══ MAIN LAYOUT ═══ --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- Desktop Sidebar --}}
        <aside class="hidden sm:flex flex-col w-60 lg:w-64 overflow-y-auto a-scroll flex-shrink-0 no-print bg-white border-r border-gray-100 shadow-sm">

            {{-- Admin card --}}
            <div class="m-4 p-4 rounded-2xl bg-indigo-50 border border-indigo-100">
                <div class="flex items-center gap-3">
                    @if(filled(auth()->user()->profile_picture) && Storage::disk('public')->exists(auth()->user()->profile_picture))
                        <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                             class="w-9 h-9 rounded-xl object-cover ring-2 ring-indigo-300 flex-shrink-0">
                    @else
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                             style="background: linear-gradient(135deg, #4f46e5, #4338ca);">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-gray-800 font-bold text-sm leading-tight truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs font-semibold mt-0.5 text-indigo-600">System Administrator</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-indigo-100">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full a-pulse bg-indigo-500"></span>
                        <p class="text-xs text-gray-400">Full Access</p>
                    </div>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 pb-4 space-y-0.5">
                @foreach($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="a-nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                        <span class="a-nav-icon">{{ $item['icon'] }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold leading-tight">{{ $item['label'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                        </div>
                        @if(request()->routeIs($item['route'].'*'))
                            <svg class="w-3 h-3 ml-auto text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Period --}}
            <div class="m-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-1.5 h-1.5 rounded-full a-pulse bg-indigo-500"></span>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Active Period</p>
                </div>
                <p class="font-bold text-gray-800 text-sm">S.Y. {{ date('Y').'-'.(date('Y')+1) }}</p>
                <p class="text-xs mt-0.5 text-indigo-600">Administrator Mode</p>
            </div>

            <div class="px-4 py-3 text-center border-t border-gray-100">
                <p class="text-gray-300 text-xs">PAC © {{ date('Y') }}</p>
            </div>
        </aside>

        {{-- ═══ MAIN CONTENT ═══ --}}
        <main class="flex-1 overflow-y-auto a-main a-scroll">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7 space-y-6">

                @if($errors->any())
                    <div class="a-alert-error a-fade flex gap-3 items-start">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>@foreach($errors->all() as $e)<p class="text-sm font-medium">{{ $e }}</p>@endforeach</div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="a-alert-success a-fade flex gap-3 items-center" x-data="{show:true}" x-show="show">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1">{{ session('success') }}</p>
                        <button @click="show=false" class="text-emerald-600 hover:text-emerald-800 text-xl leading-none font-bold">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="a-alert-error a-fade flex gap-3 items-center" x-data="{show:true}" x-show="show">
                        <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1">{{ session('error') }}</p>
                        <button @click="show=false" class="text-red-500 hover:text-red-700 text-xl leading-none font-bold">&times;</button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="a-alert-warning a-fade flex gap-3 items-center" x-data="{show:true}" x-show="show">
                        <svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1">{{ session('warning') }}</p>
                        <button @click="show=false" class="text-amber-600 hover:text-amber-800 text-xl leading-none font-bold">&times;</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <footer class="py-3 text-center text-xs no-print bg-white border-t border-gray-100 text-gray-300">
        © {{ date('Y') }} Philippine Advent College &nbsp;·&nbsp; Admin Portal &nbsp;·&nbsp; All rights reserved
    </footer>

    @stack('scripts')
</body>
</html>