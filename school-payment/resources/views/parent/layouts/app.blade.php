{{-- resources/views/parent/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Parent Portal') – PAC</title>
    @include('partials.favicon')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { background-color: #f9fafb; color: #1f2937; }
        .sidebar-link {
            color: #4b5563;
            border-left: 3px solid transparent;
        }
        .sidebar-link.active {
            background: #eef2ff;
            border-left: 3px solid #4f46e5;
            color: #4f46e5;
            font-weight: 600;
        }
        .sidebar-link:not(.active):hover {
            background: #f3f4f6;
            color: #111827;
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex" style="background-color: #f9fafb;">

    {{-- ── Sidebar ── --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200
                  transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col shadow-sm">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shadow-sm"
                 style="background: linear-gradient(135deg, #4f46e5, #6366f1);">👨‍👩‍👧</div>
            <div>
                <p class="font-bold text-sm leading-tight text-gray-800">PAC Parent Portal</p>
                <p class="text-xs text-gray-400">Philippine Advent College</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="{{ route('parent.dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home w-5 text-center text-gray-400"></i> Dashboard
            </a>

            {{-- Children list --}}
            @if(isset($students) && $students->count())
            <div class="mt-4 mb-2 px-4">
                <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">My Children</p>
            </div>
            @foreach($students as $s)
            <a href="{{ route('parent.student.detail', $s) }}"
               class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ request()->routeIs('parent.student.*') && request()->route('student')?->id == $s->id ? 'active' : '' }}">
                @if($s->profile_picture)
                    <img src="{{ asset('storage/'.$s->profile_picture) }}" class="w-6 h-6 rounded-full object-cover">
                @else
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white"
                         style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        {{ strtoupper(substr($s->name, 0, 1)) }}
                    </div>
                @endif
                <span class="truncate">{{ $s->name }} {{ $s->last_name }}</span>
            </a>
            @endforeach
            @endif

            <div class="mt-4 mb-2 px-4">
                <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">Account</p>
            </div>
            <a href="{{ route('parent.notifications') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('parent.notifications') ? 'active' : '' }}">
                <i class="fas fa-bell w-5 text-center text-gray-400"></i>
                Notifications
                @php $unread = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
                @if($unread > 0)
                    <span class="ml-auto text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
                          style="background: #4f46e5;">
                        {{ $unread > 9 ? '9+' : $unread }}
                    </span>
                @endif
            </a>
            <a href="{{ route('parent.profile') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('parent.profile*') ? 'active' : '' }}">
                <i class="fas fa-user-circle w-5 text-center text-gray-400"></i> Profile
            </a>
        </nav>

        {{-- User footer --}}
        <div class="px-4 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center gap-3">
                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/'.auth()->user()->profile_picture) }}"
                         class="w-9 h-9 rounded-full object-cover ring-2 ring-indigo-200">
                @else
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm text-white"
                         style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">Parent</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            class="text-gray-400 hover:text-red-500 transition text-sm">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Overlay (mobile) ── --}}
    <div id="overlay" onclick="closeSidebar()"
         class="fixed inset-0 z-30 bg-black/40 hidden lg:hidden"></div>

    {{-- ── Main content ── --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 bg-white border-b border-gray-200 px-4 lg:px-8 py-4 flex items-center gap-4 shadow-sm">
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-gray-700 transition">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="flex-1">
                <h1 class="text-lg font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                <p class="text-xs text-gray-400 mt-0.5">@yield('breadcrumb')</p>
                @endif
            </div>
            {{-- Notification bell --}}
            <a href="{{ route('parent.notifications') }}" class="relative text-gray-400 hover:text-indigo-600 transition">
                <i class="fas fa-bell text-lg"></i>
                @if(($unread ?? 0) > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 text-white text-xs font-bold rounded-full flex items-center justify-center"
                      style="background: #4f46e5;">
                    {{ $unread > 9 ? '9+' : $unread }}
                </span>
                @endif
            </a>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
             class="mx-4 lg:mx-8 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl flex items-center gap-3 text-sm">
            <i class="fas fa-check-circle text-emerald-500"></i>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-4 lg:mx-8 mt-4 bg-red-50 border border-red-200 text-red-600 px-5 py-3 rounded-2xl flex items-center gap-3 text-sm">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            {{ session('error') }}
        </div>
        @endif

        <main class="flex-1 p-4 lg:p-8">
            @yield('content')
        </main>

        <footer class="text-center text-xs text-gray-300 py-4 border-t border-gray-100">
            &copy; {{ date('Y') }} Philippine Advent College &mdash; Parent Portal
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('overlay');
            s.classList.toggle('-translate-x-full');
            o.classList.toggle('hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('overlay').classList.add('hidden');
        }

        // Mark notification read via AJAX
        function markRead(id, el) {
            fetch(`/parent/notifications/${id}/read`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
            }).then(() => {
                el.closest('[data-notif]')?.classList.remove('border-indigo-200', 'bg-indigo-50');
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    const n = parseInt(badge.textContent) - 1;
                    n <= 0 ? badge.remove() : (badge.textContent = n);
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>