<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth {{ auth()->user()?->dark_mode ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Treasurer') • PAC Treasurer Portal</title>
    @include('partials.favicon')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.darkmode')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Sora', sans-serif; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }

        /* ── Navigation ── */
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: 14px;
            font-weight: 600; font-size: 14px;
            color: #6b7280;
            transition: all 0.2s ease;
            position: relative; overflow: hidden;
            text-decoration: none;
        }
        .nav-link:hover { color: #1f2937; background: #f3f4f6; }
        .nav-link.active {
            background: #eef2ff;
            color: #4f46e5;
            border: 1px solid #c7d2fe;
            box-shadow: 0 2px 8px rgba(79,70,229,0.1);
        }
        .nav-link.active::before {
            content: '';
            position: absolute; left: 0; top: 25%; bottom: 25%;
            width: 3px;
            background: linear-gradient(180deg, #4f46e5, #6366f1);
            border-radius: 0 3px 3px 0;
        }
        .nav-icon {
            width: 34px; height: 34px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
            background: #f3f4f6;
            transition: all 0.2s ease;
        }
        .nav-link.active .nav-icon { background: #e0e7ff; }

        /* ── Page background ── */
        .col-main { background: #f9fafb; min-height: 100vh; }

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

        @keyframes col-pulse { 0%,100%{opacity:1} 50%{opacity:0.5} }
        .col-pulse { animation: col-pulse 2s ease-in-out infinite; }

        /* ── Badges ── */
        .col-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.05em;
        }
        .col-badge-teal    { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .col-badge-green   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .col-badge-red     { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
        .col-badge-amber   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .col-badge-sky     { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
        .col-badge-indigo  { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
        .col-badge-violet  { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }

        /* ── Alerts ── */
        .alert-success {
            background: #f0fdf4;
            border-left: 4px solid #16a34a;
            border-radius: 12px;
            padding: 14px 18px;
            color: #15803d;
        }
        .alert-error {
            background: #fff1f2;
            border-left: 4px solid #e11d48;
            border-radius: 12px;
            padding: 14px 18px;
            color: #be123c;
        }

        /* ── Scrollbar ── */
        .styled-scroll::-webkit-scrollbar { width: 4px; }
        .styled-scroll::-webkit-scrollbar-track { background: transparent; }
        .styled-scroll::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 4px; }

        /* ── Table rows ── */
        .tbl-row { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .tbl-row:hover { background: #f5f3ff; }

        /* ── Stat cards ── */
        .stat-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 24px;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .stat-card:hover { border-color: #c7d2fe; box-shadow: 0 4px 16px rgba(79,70,229,0.08); }

        /* ── Form inputs ── */
        .form-input {
            width: 100%;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 10px 14px;
            color: #1f2937;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        .form-input option { background: #ffffff; color: #1f2937; }
        .form-input::placeholder { color: #9ca3af; }

        /* ── Buttons ── */
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(79,70,229,0.2);
        }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }

        .btn-secondary {
            background: #ffffff;
            color: #374151;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid #d1d5db;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-secondary:hover { background: #f3f4f6; border-color: #9ca3af; color: #1f2937; }

        .btn-danger {
            background: #fff1f2;
            color: #e11d48;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid #fecdd3;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-danger:hover { background: #ffe4e6; }

        /* ── Section cards ── */
        .section-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        /* ── Progress bars ── */
        .progress-bar-track {
            background: #e5e7eb;
            border-radius: 999px;
            height: 6px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #4f46e5, #6366f1);
            transition: width 1s cubic-bezier(.4,0,.2,1);
        }

        .card-lift { transition: transform 0.22s ease, box-shadow 0.22s ease; }
        .card-lift:hover { transform: translateY(-3px); box-shadow: 0 12px 36px rgba(79,70,229,0.1); }
    </style>
</head>

<body class="h-full flex flex-col"
      style="background-color: var(--bg);"
      x-data="{
          mobileMenuOpen: false,
          dark: {{ auth()->user()?->dark_mode ? 'true' : 'false' }},
          async toggleDark() {
              this.dark = !this.dark;
              document.documentElement.classList.toggle('dark', this.dark);
              try {
                  const res = await fetch('{{ route('user.settings.dark-mode') }}', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                      },
                      body: JSON.stringify({ dark_mode: this.dark })
                  });
                  if (!res.ok) {
                      this.dark = !this.dark;
                      document.documentElement.classList.toggle('dark', this.dark);
                      console.error('Dark mode save failed — HTTP ' + res.status);
                  }
              } catch(e) {
                  this.dark = !this.dark;
                  document.documentElement.classList.toggle('dark', this.dark);
                  console.error('Dark mode network error:', e);
              }
          }
      }"
      @keydown.escape="mobileMenuOpen = false">

    <!-- ═══ TOP HEADER ═══ -->
    <header class="sticky top-0 z-40 flex items-center justify-between px-4 py-3 sm:px-6 bg-white shadow-sm border-b border-gray-100">
        <div class="flex items-center gap-3">
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="sm:hidden w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-lg font-bold shadow-sm"
                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);">💰</div>
                <div>
                    <span class="text-gray-800 font-bold text-sm leading-tight block">PAC Treasurer</span>
                    <span class="text-xs font-semibold block text-indigo-500">Finance Portal</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="hidden sm:block text-xs font-semibold px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100">
                S.Y. {{ date('n') >= 8 ? date('Y').'-'.(date('Y')+1) : (date('Y')-1).'-'.date('Y') }}
            </span>
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
            <a href="{{ route('treasurer.profile') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-xl text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition text-sm font-semibold">
                @if(filled(auth()->user()->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->profile_picture))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->profile_picture) }}"
                         class="rounded-full object-cover flex-shrink-0"
                         style="width:28px; height:28px; min-width:28px; max-width:28px; max-height:28px;">
                @else
                    <div class="rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                         style="width:28px; height:28px; min-width:28px; background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        {{ strtoupper(substr(auth()->user()->name ?? 'T', 0, 1)) }}
                    </div>
                @endif
                <span class="hidden sm:block text-gray-700">{{ auth()->user()->name }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                        title="Logout">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <!-- Mobile drawer -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-220"
         x-transition:enter-start="opacity-0 -translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-180"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 -translate-x-full"
         class="sm:hidden fixed top-[52px] left-0 bottom-0 z-50 w-72 overflow-y-auto styled-scroll shadow-2xl bg-white border-r border-gray-100">
        <div class="flex items-center justify-between px-4 pt-4 pb-2 border-b border-gray-100">
            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Navigation</span>
            <button @click="mobileMenuOpen = false"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition text-sm">✕</button>
        </div>
        @include('treasurer.partials.nav', ['mobile' => true])
    </div>

    <!-- ═══ MAIN LAYOUT ═══ -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Desktop Sidebar -->
        <aside class="hidden sm:flex flex-col w-64 lg:w-72 overflow-y-auto styled-scroll flex-shrink-0 bg-white border-r border-gray-100 shadow-sm">
            @include('treasurer.partials.nav', ['mobile' => false])
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto col-main styled-scroll">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-6">

                @if($errors->any())
                    <div class="alert-error fade-up flex gap-3 items-start">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
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
                        <svg class="w-5 h-5 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold flex-1 text-green-800">{{ session('success') }}</p>
                        <button @click="show=false" class="text-xl leading-none font-bold text-green-600">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-error fade-up flex gap-3 items-center" x-data="{show:true}" x-show="show">
                        <p class="text-sm font-semibold flex-1">{{ session('error') }}</p>
                        <button @click="show=false" class="text-xl leading-none font-bold text-red-500">&times;</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <footer class="py-3 text-center text-xs bg-white border-t border-gray-100 text-gray-300">
        © {{ date('Y') }} PAC &nbsp;·&nbsp; Treasurer Finance Portal
    </footer>

    @stack('scripts')
</body>
</html>