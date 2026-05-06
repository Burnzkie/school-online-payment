{{-- resources/views/students/hs/layouts/hs-app.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth {{ auth()->user()?->dark_mode ? 'dark' : '' }}">
{{--
    ↑ 'dark' class is set SERVER-SIDE from the database.
    Correct theme applied instantly — zero flicker.
--}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') • PAC Student Portal</title>
    @include('partials.favicon')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,700&family=Righteous&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* ── HS portal inherits global theme vars from app.css ── */
        :root {
            --hs-violet:   var(--primary);
            --hs-violet-l: var(--primary-muted);
            --hs-pink:     #ec4899;
            --hs-cyan:     var(--accent);
            --hs-amber:    var(--warning);
            --hs-emerald:  var(--success);
            --hs-rose:     var(--danger);
            --hs-dark:     var(--bg);
            --hs-dark2:    var(--bg2);
            --hs-dark3:    var(--primary-light);
            --hs-border:   var(--border);
            --hs-text:     var(--text);
            --hs-muted:    var(--muted);
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        .font-display  { font-family: 'Righteous', cursive; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }

        /* ── Sidebar nav ── */
        .hs-nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 16px; border-radius: 14px;
            font-weight: 600; font-size: 14px;
            color: var(--muted);
            transition: all 0.2s ease;
            position: relative; overflow: hidden;
            text-decoration: none;
        }
        .hs-nav-link:hover {
            color: var(--primary);
            background: var(--primary-light);
        }
        .hs-nav-link.active {
            background: var(--primary-light);
            color: var(--primary);
            border-left: 3px solid var(--primary);
            font-weight: 700;
        }
        .hs-nav-link.active::before { display: none; }
        .hs-nav-icon {
            width: 34px; height: 34px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
            background: var(--bg3);
            transition: all 0.2s ease;
        }
        .hs-nav-link.active .hs-nav-icon {
            background: var(--primary-light);
        }

        /* ── Card hover ── */
        .hs-card-lift {
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }
        .hs-card-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(29,78,216,0.12);
        }

        /* ── Header ── */
        .hs-header {
            background: var(--header-bg);
            border-bottom: 1px solid var(--header-border);
            box-shadow: var(--header-shadow);
        }

        /* ── Sidebar ── */
        .hs-sidebar {
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
        }

        /* ── Main ── */
        .hs-main {
            background: var(--bg);
            min-height: 100vh;
        }

        /* ── Student card in sidebar ── */
        .hs-student-card {
            background: var(--primary-light);
            border: 1px solid var(--primary-border);
        }

        /* ── Period widget ── */
        .hs-period-widget {
            background: var(--bg3);
            border: 1px solid var(--border);
        }

        /* ── Mobile menu ── */
        .hs-mobile-menu {
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
        }
        .hs-mobile-header {
            background: var(--bg3);
        }
        .hs-mobile-sy {
            background: var(--primary-light);
            border: 1px solid var(--primary-border);
        }

        /* ── Footer ── */
        .hs-footer {
            background: var(--bg2);
            border-top: 1px solid var(--border);
            color: var(--muted-light);
        }

        /* ── Gradient text ── */
        .grad-text {
            background: linear-gradient(135deg, var(--primary), var(--primary-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .grad-text-cyan {
            background: linear-gradient(135deg, var(--accent), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Animations ── */
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
        .hs-shimmer { position: relative; overflow: hidden; }
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

        @keyframes hs-bar-fill { from { width: 0%; } }
        .hs-bar-anim { animation: hs-bar-fill 1.4s cubic-bezier(.4,0,.2,1) both; }

        /* ── Badges ── */
        .hs-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.05em;
        }
        .hs-badge-sky    { background: var(--info-light);    color: var(--info);    border: 1px solid var(--info-border); }
        .hs-badge-cyan   { background: var(--accent-light);  color: var(--accent);  border: 1px solid var(--accent-border); }
        .hs-badge-violet { background: var(--primary-light); color: var(--primary); border: 1px solid var(--primary-border); }
        .hs-badge-green  { background: var(--success-light); color: var(--success); border: 1px solid var(--success-border); }
        .hs-badge-red    { background: var(--danger-light);  color: var(--danger);  border: 1px solid var(--danger-border); }
        .hs-badge-amber  { background: var(--warning-light); color: var(--warning); border: 1px solid var(--warning-border); }

        /* ── Alert banners ── */
        .hs-alert-success {
            background: var(--success-light);
            border-left: 4px solid var(--success);
            color: var(--success);
            padding: 14px 18px; border-radius: 12px;
            border: 1px solid var(--success-border);
        }
        .hs-alert-error {
            background: var(--danger-light);
            border-left: 4px solid var(--danger);
            color: var(--danger);
            padding: 14px 18px; border-radius: 12px;
            border: 1px solid var(--danger-border);
        }

        /* ── Scrollbar ── */
        .hs-scroll::-webkit-scrollbar { width: 5px; }
        .hs-scroll::-webkit-scrollbar-track { background: transparent; }
        .hs-scroll::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 3px; }

        /* ── Mobile nav backdrop ── */
        .hs-backdrop { background: rgba(17,24,39,0.5); backdrop-filter: blur(6px); }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen antialiased flex flex-col"
      x-data="{
          mobileMenuOpen: false,

          /* Dark mode — initial value from DB (server-rendered into HTML class) */
          dark: {{ auth()->user()?->dark_mode ? 'true' : 'false' }},

          /* Toggle: flips the .dark class on <html> and saves to DB via AJAX */
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
      @keydown.escape="mobileMenuOpen = false"
      style="background: var(--bg);">

    @php
        $user = auth()->user();
        $isJHS = $user && str_contains(strtolower($user->level_group ?? ''), 'junior');
        $isSHS = $user && str_contains(strtolower($user->level_group ?? ''), 'senior');
        $levelLabel = $isJHS ? 'Junior High' : ($isSHS ? 'Senior High' : 'High School');
        $accentColor = $isJHS ? '#06b6d4' : '#06b6d4';

        $nav = [
            ['route' => 'hs.dashboard',    'label' => 'Dashboard',    'icon' => '🏠', 'desc' => 'My Overview'],
            ['route' => 'hs.billing',       'label' => 'Billing',      'icon' => '₱',  'desc' => 'Fees & Charges'],
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
                            class="sm:hidden w-9 h-9 rounded-xl flex items-center justify-center transition-all"
                            style="color: var(--muted)"
                            onmouseover="this.style.background='var(--bg3)'; this.style.color='var(--text)'"
                            onmouseout="this.style.background=''; this.style.color='var(--muted)'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-base"
                             style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                            🎓
                        </div>
                        <div class="hidden sm:block">
                            <p class="font-display text-base leading-none tracking-wide" style="color: var(--text)">PAC Portal</p>
                            <p class="text-[10px] font-semibold mt-0.5" style="color: var(--accent);">{{ $levelLabel }} School</p>
                        </div>
                    </div>
                </div>

                <!-- Right: user + dark toggle + logout -->
                <div class="flex items-center gap-2">

                    <!-- User pill -->
                    <div class="hidden sm:flex items-center gap-2.5 px-3.5 py-2 rounded-xl"
                         style="background: var(--bg3); border: 1px solid var(--border);">
                        @if(filled(auth()->user()->profile_picture) && Storage::disk('public')->exists(auth()->user()->profile_picture))
                            <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                                 class="w-5 h-5 rounded-full object-cover"
                                 style="outline: 1px solid var(--primary-border)">
                        @else
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white"
                                 style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-semibold leading-tight" style="color: var(--text)">
                                {{ explode(' ', auth()->user()->name ?? 'Student')[0] }}
                            </p>
                            <p class="text-[10px]" style="color: var(--accent);">{{ $levelLabel }}</p>
                        </div>
                    </div>

                    <!-- ── Dark Mode Toggle ── -->
                    <button @click="toggleDark()"
                            class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200"
                            style="color: var(--muted);"
                            onmouseover="this.style.background='var(--bg3)'; this.style.color='var(--text)'"
                            onmouseout="this.style.background=''; this.style.color='var(--muted)'"
                            :title="dark ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                        <!-- Moon icon (shown in light mode) -->
                        <svg x-show="!dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75
                                     0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21
                                     12.75 21a9.753 9.753 0 009.002-5.998z"/>
                        </svg>
                        <!-- Sun icon (shown in dark mode) -->
                        <svg x-show="dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591
                                     M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636
                                     5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                        </svg>
                    </button>

                    <!-- Notification bell -->
                    <button class="w-9 h-9 rounded-xl flex items-center justify-center transition-all relative"
                            style="color: var(--muted)"
                            onmouseover="this.style.background='var(--bg3)'; this.style.color='var(--primary)'"
                            onmouseout="this.style.background=''; this.style.color='var(--muted)'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>

                    <!-- Logout -->
                    <a href="{{ route('logout') }}"
                       @click.prevent="if(confirm('Log out?')) { document.getElementById('hs-logout-form').submit() }"
                       class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-all"
                       style="color: var(--muted)"
                       onmouseover="this.style.color='var(--danger)'; this.style.background='var(--danger-light)'"
                       onmouseout="this.style.color='var(--muted)'; this.style.background=''">
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
         class="sm:hidden fixed top-[52px] left-0 bottom-0 z-50 w-72 overflow-y-auto hs-scroll shadow-2xl hs-mobile-menu">

        <div class="p-4 border-b hs-mobile-header flex items-center justify-between"
             style="border-color: var(--border)">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold text-white shadow-xl"
                     style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                    {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-base" style="color: var(--text)">{{ auth()->user()->name ?? 'Student' }}</p>
                    <p class="text-xs font-semibold" style="color: var(--primary)">{{ $levelLabel }} School</p>
                </div>
            </div>
            <button @click="mobileMenuOpen = false"
                    class="w-8 h-8 rounded-xl flex items-center justify-center transition-all flex-shrink-0"
                    style="color: var(--muted)"
                    onmouseover="this.style.color='var(--danger)'; this.style.background='var(--danger-light)'"
                    onmouseout="this.style.color='var(--muted)'; this.style.background=''">
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
                        <p class="text-xs mt-0.5" style="color: var(--muted-light)">{{ $item['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </nav>

        <div class="p-5 mx-4 mb-4 rounded-2xl hs-mobile-sy">
            <p class="text-xs font-bold mb-1" style="color: var(--primary)">School Year</p>
            <p class="font-bold text-sm" style="color: var(--text)">{{ date('Y') . '-' . (date('Y')+1) }}</p>
            @if($isSHS)
                <p class="text-xs mt-1" style="color: var(--primary-muted)">Semester available</p>
            @else
                <p class="text-xs mt-1" style="color: var(--primary-muted)">Full year billing</p>
            @endif
        </div>
    </div>

    <!-- ═══════════════ MAIN LAYOUT ═══════════════ -->
    <div class="flex flex-1 overflow-hidden">

        <!-- Desktop Sidebar -->
        <aside class="hidden sm:flex flex-col w-64 lg:w-72 overflow-y-auto hs-scroll flex-shrink-0 shadow-sm hs-sidebar">

            <!-- Student card -->
            <div class="m-4 p-4 rounded-2xl hs-shimmer hs-student-card">
                <div class="flex items-center gap-3">
                    @if(filled(auth()->user()->profile_picture) && Storage::disk('public')->exists(auth()->user()->profile_picture))
                        <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                             class="w-6 h-6 rounded-lg object-cover flex-shrink-0"
                             style="outline: 1px solid var(--primary-border)">
                    @else
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0 shadow-lg"
                             style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                            {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-bold text-sm leading-tight truncate" style="color: var(--text)">{{ auth()->user()->name ?? 'Student' }}</p>
                        <p class="text-xs font-semibold mt-0.5" style="color: var(--primary)">{{ auth()->user()->student_id ?? $levelLabel }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3" style="border-top: 1px solid var(--primary-border)">
                    <p class="text-xs" style="color: var(--muted)">Year Level</p>
                    <p class="text-sm font-bold mt-0.5" style="color: var(--text)">{{ auth()->user()->year_level ?? 'N/A' }}</p>
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
                            <p class="text-xs mt-0.5" style="color: var(--muted-light)">{{ $item['desc'] }}</p>
                        </div>
                        @if(request()->routeIs($item['route'].'*'))
                            <svg class="w-4 h-4 ml-auto flex-shrink-0" style="color: var(--primary-muted); opacity: 0.6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    </a>
                @endforeach
            </nav>

            <!-- School year tag -->
            <div class="m-4 p-4 rounded-2xl hs-period-widget">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full hs-pulse" style="background: var(--accent);"></span>
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--muted-light)">Current Period</p>
                </div>
                <p class="font-bold text-sm" style="color: var(--text)">S.Y. {{ date('Y') . '–' . (date('Y')+1) }}</p>
                @if($isSHS)
                    <p class="text-xs mt-1" style="color: var(--primary)">Senior High — Semester Based</p>
                @else
                    <p class="text-xs mt-1" style="color: var(--primary)">Junior High — Annual Billing</p>
                @endif
            </div>

            <div class="px-4 py-3 text-center" style="border-top: 1px solid var(--border)">
                <p class="text-xs" style="color: var(--muted-light)">PAC © {{ date('Y') }}</p>
            </div>
        </aside>

        <!-- ═══════════ MAIN CONTENT ═══════════ -->
        <main class="flex-1 overflow-y-auto hs-main hs-scroll">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-6">

                @if($errors->any())
                    <div class="hs-alert-error hs-fade flex gap-3 items-start">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" style="color: var(--danger);">
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
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="color: var(--success);">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1">{{ session('success') }}</p>
                        <button @click="show=false" class="text-xl leading-none font-bold" style="color: var(--success)">&times;</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs hs-footer">
        © {{ date('Y') }} PAC &nbsp;·&nbsp; {{ $levelLabel }} School Payment Portal
    </footer>

    @stack('scripts')
</body>
</html>