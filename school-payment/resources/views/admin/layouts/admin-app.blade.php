{{-- resources/views/admin/layouts/admin-app.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth {{ auth()->user()?->dark_mode ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') • PAC Admin Portal</title>
    @include('partials.favicon')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --a-bg:      var(--bg);
            --a-bg2:     var(--bg2);
            --a-bg3:     var(--bg3);
            --a-blue:    var(--primary);
            --a-blue2:   var(--primary-muted);
            --a-rose:    var(--danger);
            --a-amber:   var(--warning);
            --a-emerald: var(--success);
            --a-sky:     var(--accent);
            --a-border:  var(--border);
            --a-text:    var(--text);
            --a-muted:   var(--muted);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
        }

        /* ── Force dark background on html/body ── */
        html.dark,
        html.dark body {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
        }

        .font-mono-num { font-family: 'JetBrains Mono', monospace; }

        /* ── Layout ── */
        .a-layout-wrapper {
            display: flex;
            height: calc(100vh - 56px); /* subtract header height */
            overflow: hidden;
        }

        /* ── Desktop Sidebar (always visible, NOT fixed) ── */
        .a-sidebar-desktop {
            width: 256px;
            flex-shrink: 0;
            overflow-y: auto;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
        }

        /* ── Mobile Sidebar (fixed drawer) ── */
        .a-sidebar-mobile {
            position: fixed;
            top: 56px; /* below header */
            left: 0;
            bottom: 0;
            width: 256px;
            z-index: 50;
            overflow-y: auto;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .a-sidebar-mobile.open {
            transform: translateX(0);
        }

        /* ── Main content ── */
        .a-main {
            flex: 1;
            overflow-y: auto;
            background-color: var(--bg);
            min-width: 0; /* prevent flex overflow */
        }
        html.dark .a-main {
            background-color: #0f172a !important;
        }

        /* ── Header ── */
        .a-header {
            background: var(--header-bg);
            border-bottom: 1px solid var(--header-border);
            box-shadow: var(--header-shadow);
            height: 56px;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        html.dark .a-header {
            background: #1e293b !important;
            border-color: #334155 !important;
        }
        html.dark .a-sidebar-desktop,
        html.dark .a-sidebar-mobile {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        /* ── Nav links ── */
        .a-nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 13px; border-radius: 14px;
            font-weight: 600; font-size: 13px;
            color: var(--muted);
            transition: all 0.2s ease;
            position: relative; overflow: hidden;
            text-decoration: none;
        }
        .a-nav-link:hover { color: var(--text); background: var(--bg3); }
        .a-nav-link.active {
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid var(--primary-border);
            box-shadow: 0 2px 8px rgba(29,78,216,0.1);
        }
        .a-nav-link.active::before {
            content: '';
            position: absolute; left: 0; top: 25%; bottom: 25%;
            width: 3px;
            background: linear-gradient(180deg, var(--primary), var(--primary-muted));
            border-radius: 0 3px 3px 0;
        }
        .a-nav-icon {
            width: 30px; height: 30px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
            background: var(--bg3);
            transition: all 0.2s ease;
        }
        .a-nav-link.active .a-nav-icon { background: var(--primary-light); }

        /* ── Cards / components ── */
        .a-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        html.dark .a-card {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        .a-stat-card {
            background: var(--bg2); border: 1px solid var(--border);
            border-radius: 20px; padding: 20px 22px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        html.dark .a-stat-card {
            background: #1e293b !important;
            border-color: #334155 !important;
        }
        .a-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(29,78,216,0.1); }

        /* ── Table ── */
        .a-table { width: 100%; border-collapse: collapse; }
        .a-table th {
            text-align: left; padding: 11px 14px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.07em;
            text-transform: uppercase; color: var(--muted-light);
            border-bottom: 1px solid var(--border); white-space: nowrap;
            background: var(--bg3);
        }
        .a-table td { padding: 13px 14px; border-bottom: 1px solid var(--border); font-size: 13.5px; color: var(--text-secondary); }
        .a-table tr:last-child td { border-bottom: none; }
        .a-table tr:hover td { background: var(--bg3); }

        /* ── Badges ── */
        .a-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: 0.04em; }
        .a-badge-violet  { background: var(--primary-light); color: var(--primary);  border: 1px solid var(--primary-border); }
        .a-badge-emerald { background: var(--success-light); color: var(--success);  border: 1px solid var(--success-border); }
        .a-badge-red     { background: var(--danger-light);  color: var(--danger);   border: 1px solid var(--danger-border); }
        .a-badge-amber   { background: var(--warning-light); color: var(--warning);  border: 1px solid var(--warning-border); }
        .a-badge-sky     { background: var(--info-light);    color: var(--info);     border: 1px solid var(--info-border); }
        .a-badge-gray    { background: var(--bg3);           color: var(--muted);    border: 1px solid var(--border); }

        /* ── Inputs ── */
        .a-input {
            width: 100%; background: var(--bg2); border: 1px solid var(--border-strong);
            border-radius: 12px; padding: 10px 14px; color: var(--text);
            font-size: 14px; font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.2s ease;
        }
        .a-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(29,78,216,0.12); }
        .a-input::placeholder { color: var(--muted-light); }
        .a-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 12px center;
            background-size: 16px; padding-right: 40px !important;
        }

        /* ── Buttons ── */
        .a-btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #fff; border: none; padding: 10px 20px; border-radius: 12px;
            font-weight: 700; font-size: 13.5px; cursor: pointer; transition: all 0.2s ease;
        }
        .a-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(29,78,216,0.3); }
        .a-btn-secondary {
            background: var(--bg2); color: var(--text-secondary); border: 1px solid var(--border-strong);
            padding: 9px 18px; border-radius: 12px; font-weight: 600; font-size: 13px;
            cursor: pointer; transition: all 0.2s ease;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .a-btn-secondary:hover { background: var(--bg3); border-color: var(--muted-light); color: var(--text); }
        .a-btn-danger {
            background: var(--danger-light); color: var(--danger); border: 1px solid var(--danger-border);
            padding: 8px 16px; border-radius: 10px; font-weight: 600; font-size: 12.5px;
            cursor: pointer; transition: all 0.2s ease;
        }
        .a-btn-danger:hover { filter: brightness(0.95); }

        /* ── Alerts ── */
        .a-alert-success { background: var(--success-light); border-left: 4px solid var(--success); color: var(--success); padding: 14px 18px; border-radius: 12px; }
        .a-alert-error   { background: var(--danger-light);  border-left: 4px solid var(--danger);  color: var(--danger);  padding: 14px 18px; border-radius: 12px; }
        .a-alert-warning { background: var(--warning-light); border-left: 4px solid var(--warning); color: var(--warning); padding: 14px 18px; border-radius: 12px; }

        /* ── Animations ── */
        @keyframes a-fade-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
        .a-fade { animation: a-fade-up 0.4s ease both; }
        .a-d1 { animation-delay: 0.05s; } .a-d2 { animation-delay: 0.10s; }
        .a-d3 { animation-delay: 0.15s; } .a-d4 { animation-delay: 0.20s; }
        .a-d5 { animation-delay: 0.25s; }

        @keyframes a-pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .a-pulse { animation: a-pulse 2s ease-in-out infinite; }

        /* ── Scrollbar ── */
        .a-scroll::-webkit-scrollbar { width: 5px; }
        .a-scroll::-webkit-scrollbar-track { background: transparent; }
        .a-scroll::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 3px; }

        /* ── Progress ── */
        .a-progress-track { background: var(--bg3); border-radius: 999px; height: 6px; }
        .a-progress-fill  { background: linear-gradient(90deg, var(--primary), var(--primary-muted)); border-radius: 999px; height: 6px; transition: width 0.5s ease; }

        /* ── Print ── */
        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }

        /* ── Responsive: hide desktop sidebar on mobile ── */
        @media (max-width: 1023px) {
            .a-sidebar-desktop { display: none !important; }
        }
        @media (min-width: 1024px) {
            .a-sidebar-mobile { display: none !important; }
        }
    </style>

    @stack('styles')
</head>

<body class="antialiased no-print"
      x-data="{
          mobileMenuOpen: false,
          dark: {{ auth()->user()?->dark_mode ? 'true' : 'false' }},
          async toggleDark() {
              this.dark = !this.dark;
              document.documentElement.classList.toggle('dark', this.dark);
              try {
                  await fetch('{{ route('user.settings.dark-mode') }}', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                      },
                      body: JSON.stringify({ dark_mode: this.dark })
                  });
              } catch (e) {
                  console.warn('Dark mode save failed:', e);
              }
          }
      }"
      @keydown.escape="mobileMenuOpen = false">

    {{-- ══ HEADER ══════════════════════════════════════════════════ --}}
    <header class="a-header no-print">
        <div class="flex items-center justify-between h-full px-4 lg:px-6 gap-3">

            {{-- Left: Hamburger (mobile only) + Logo --}}
            <div class="flex items-center gap-3">
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden w-9 h-9 flex items-center justify-center rounded-xl transition-colors"
                        style="color: var(--muted)"
                        onmouseover="this.style.background='var(--bg3)'"
                        onmouseout="this.style.background=''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white font-bold text-sm"
                         style="background: linear-gradient(135deg, var(--primary), var(--accent));">P</div>
                    <div class="hidden sm:block">
                        <p class="font-bold text-base leading-none" style="color: var(--text)">PAC Admin</p>
                        <p class="text-[10px] font-semibold mt-0.5" style="color: var(--primary)">System Administrator</p>
                    </div>
                </div>
            </div>

            {{-- Right: SY badge + Dark toggle + User + Logout --}}
            <div class="flex items-center gap-2">

                {{-- School Year --}}
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl"
                     style="background: var(--primary-light); border: 1px solid var(--primary-border);">
                    <span class="w-1.5 h-1.5 rounded-full a-pulse" style="background: var(--primary)"></span>
                    <span class="text-xs font-semibold" style="color: var(--primary)">
                        SY {{ date('Y').'-'.(date('Y')+1) }}
                    </span>
                </div>

                {{-- Dark Mode Toggle --}}
                <button @click="toggleDark()"
                        class="w-9 h-9 flex items-center justify-center rounded-xl transition-all duration-200"
                        style="color: var(--muted);"
                        onmouseover="this.style.background='var(--bg3)'; this.style.color='var(--text)'"
                        onmouseout="this.style.background=''; this.style.color='var(--muted)'"
                        :title="dark ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                    <svg x-show="!dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75
                                 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21
                                 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                    <svg x-show="dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591
                                 M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636
                                 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    </svg>
                </button>

                {{-- User info --}}
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl"
                     style="background: var(--bg3); border: 1px solid var(--border);">
                    @if(filled(auth()->user()->profile_picture) && Storage::disk('public')->exists(auth()->user()->profile_picture))
                        <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                             class="w-5 h-5 rounded-full object-cover"
                             style="outline: 1px solid var(--primary-border)">
                    @else
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white"
                             style="background: linear-gradient(135deg, var(--primary), var(--primary-hover));">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-semibold leading-tight" style="color: var(--text)">
                            {{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}
                        </p>
                        <p class="text-[10px]" style="color: var(--primary)">Admin</p>
                    </div>
                </div>

                {{-- Logout --}}
                <a href="{{ route('logout') }}"
                   @click.prevent="if(confirm('Log out?')) { document.getElementById('a-logout-form').submit() }"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-all"
                   style="color: var(--muted)"
                   onmouseover="this.style.color='var(--text)'; this.style.background='var(--bg3)'"
                   onmouseout="this.style.color='var(--muted)'; this.style.background=''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="hidden sm:inline">Logout</span>
                </a>
                <form id="a-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </header>

    {{-- ══ LAYOUT: Sidebar + Main ══════════════════════════════════ --}}
    <div class="a-layout-wrapper">

        {{-- ── DESKTOP Sidebar (always visible, part of flex flow) ── --}}
        <aside class="a-sidebar-desktop a-scroll no-print">
            <div class="p-4 space-y-1 flex-1">
                <p class="px-3 pt-3 pb-1 text-[10px] font-bold tracking-widest uppercase"
                   style="color: var(--muted-light)">Main Menu</p>

                <a href="{{ route('admin.dashboard') }}"
                   class="a-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="a-nav-icon">🏠</span> Dashboard
                </a>
                <a href="{{ route('admin.students') }}"
                   class="a-nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                    <span class="a-nav-icon">🎓</span> Students
                </a>
                <a href="{{ route('admin.fees') }}"
                   class="a-nav-link {{ request()->routeIs('admin.fees*') ? 'active' : '' }}">
                    <span class="a-nav-icon">💳</span> Fees
                </a>
                <a href="{{ route('admin.payments') }}"
                   class="a-nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                    <span class="a-nav-icon">💰</span> Payments
                </a>
                
                <a href="{{ route('admin.scholarships') }}"
                   class="a-nav-link {{ request()->routeIs('admin.scholarships*') ? 'active' : '' }}">
                    <span class="a-nav-icon">🏆</span> Scholarships
                </a>
                <a href="{{ route('admin.clearances') }}"
                   class="a-nav-link {{ request()->routeIs('admin.clearances*') ? 'active' : '' }}">
                    <span class="a-nav-icon">✅</span> Clearances
                </a>
                <a href="{{ route('admin.reports') }}"
                   class="a-nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                    <span class="a-nav-icon">📊</span> Reports
                </a>

                <p class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-widest uppercase"
                   style="color: var(--muted-light)">Administration</p>

                <a href="{{ route('admin.users') }}"
                   class="a-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <span class="a-nav-icon">👥</span> Users
                </a>
                <a href="{{ route('admin.profile') }}"
                   class="a-nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                    <span class="a-nav-icon">👤</span> Profile
                </a>
            </div>
        </aside>

        {{-- ── MOBILE Sidebar (fixed drawer, separate from desktop) ── --}}
        <aside class="a-sidebar-mobile a-scroll no-print"
               :class="mobileMenuOpen ? 'open' : ''">
            <div class="p-4 space-y-1">

                {{-- Mobile close button --}}
                <div class="flex items-center justify-between pb-2 mb-2"
                     style="border-bottom: 1px solid var(--border)">
                    <p class="text-sm font-bold" style="color: var(--text)">Menu</p>
                    <button @click="mobileMenuOpen = false"
                            class="w-8 h-8 rounded-xl flex items-center justify-center"
                            style="color: var(--muted)"
                            onmouseover="this.style.background='var(--danger-light)'; this.style.color='var(--danger)'"
                            onmouseout="this.style.background=''; this.style.color='var(--muted)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <p class="px-3 pt-2 pb-1 text-[10px] font-bold tracking-widest uppercase"
                   style="color: var(--muted-light)">Main Menu</p>

                <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false"
                   class="a-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="a-nav-icon">🏠</span> Dashboard
                </a>
                <a href="{{ route('admin.students') }}" @click="mobileMenuOpen = false"
                   class="a-nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                    <span class="a-nav-icon">🎓</span> Students
                </a>
                <a href="{{ route('admin.fees') }}" @click="mobileMenuOpen = false"
                   class="a-nav-link {{ request()->routeIs('admin.fees*') ? 'active' : '' }}">
                    <span class="a-nav-icon">💳</span> Fees
                </a>
                <a href="{{ route('admin.payments') }}" @click="mobileMenuOpen = false"
                   class="a-nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                    <span class="a-nav-icon">💰</span> Payments
                </a>
                
                <a href="{{ route('admin.scholarships') }}" @click="mobileMenuOpen = false"
                   class="a-nav-link {{ request()->routeIs('admin.scholarships*') ? 'active' : '' }}">
                    <span class="a-nav-icon">🏆</span> Scholarships
                </a>
                <a href="{{ route('admin.clearances') }}" @click="mobileMenuOpen = false"
                   class="a-nav-link {{ request()->routeIs('admin.clearances*') ? 'active' : '' }}">
                    <span class="a-nav-icon">✅</span> Clearances
                </a>
                <a href="{{ route('admin.reports') }}" @click="mobileMenuOpen = false"
                   class="a-nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                    <span class="a-nav-icon">📊</span> Reports
                </a>

                <p class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-widest uppercase"
                   style="color: var(--muted-light)">Administration</p>

                <a href="{{ route('admin.users') }}" @click="mobileMenuOpen = false"
                   class="a-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <span class="a-nav-icon">👥</span> Users
                </a>
                <a href="{{ route('admin.profile') }}" @click="mobileMenuOpen = false"
                   class="a-nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                    <span class="a-nav-icon">👤</span> Profile
                </a>
            </div>
        </aside>

        {{-- Mobile overlay backdrop --}}
        <div x-show="mobileMenuOpen"
             @click="mobileMenuOpen = false"
             class="lg:hidden fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
             style="top: 56px"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        {{-- ── MAIN CONTENT ── --}}
        <main class="a-main a-scroll">
            <div class="p-6 space-y-6">

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="a-alert-success a-fade flex gap-3 items-center"
                         x-data="{show:true}" x-show="show">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1">{{ session('success') }}</p>
                        <button @click="show=false" class="text-xl font-bold leading-none">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="a-alert-error a-fade flex gap-3 items-center"
                         x-data="{show:true}" x-show="show">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1">{{ session('error') }}</p>
                        <button @click="show=false" class="text-xl font-bold leading-none">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="a-alert-error a-fade">
                        @foreach($errors->all() as $error)
                            <p class="text-sm font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>