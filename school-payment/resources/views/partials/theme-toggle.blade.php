{{--
    resources/views/partials/theme-toggle.blade.php
    ─────────────────────────────────────────────────────────────
    DB-backed dark mode — reads from auth()->user()->dark_mode
    Auto-saves via AJAX on toggle. Works for ALL roles.

    HOW TO USE:
    ① Include ONCE inside <head> of every layout (before @vite):
        @include('partials.theme-toggle')

    ② Paste the BUTTON HTML (Section B below) inside your header.

    ③ For the Settings page toggle, use Section C below.
    ─────────────────────────────────────────────────────────────
--}}

{{-- ═══════════════════════════════════════════════════════════
     SECTION A — Server-side class injection (no flash)
     This sets the 'dark' class BEFORE CSS loads,
     so there is zero flicker on page load.
     ═══════════════════════════════════════════════════════════ --}}
@auth
    @if(auth()->user()->dark_mode)
        <script>document.documentElement.classList.add('dark');</script>
    @else
        <script>document.documentElement.classList.remove('dark');</script>
    @endif
@endauth

{{-- CSRF token for AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">


{{--
    ═══════════════════════════════════════════════════════════
    SECTION B — Header toggle button (small moon/sun icon)
    Paste this HTML inside your header's right-side buttons area.
    ═══════════════════════════════════════════════════════════

    <div x-data="{
            dark: {{ auth()->user()?->dark_mode ? 'true' : 'false' }},
            async toggle() {
                this.dark = !this.dark;
                document.documentElement.classList.toggle('dark', this.dark);
                await fetch('{{ route('user.settings.dark-mode') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ dark_mode: this.dark })
                });
            }
         }">
        <button @click="toggle()"
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
    </div>

    ═══════════════════════════════════════════════════════════
    SECTION C — Settings page toggle (bigger, with label)
    Paste inside your settings blade page.
    ═══════════════════════════════════════════════════════════

    <div x-data="{
            dark: {{ auth()->user()?->dark_mode ? 'true' : 'false' }},
            saving: false,
            async toggle() {
                this.dark = !this.dark;
                this.saving = true;
                document.documentElement.classList.toggle('dark', this.dark);
                await fetch('{{ route('user.settings.dark-mode') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ dark_mode: this.dark })
                });
                this.saving = false;
            }
         }">

        <div class="a-card p-5 mb-4">
            <h3 class="font-bold text-base mb-4" style="color: var(--text)">🎨 Appearance</h3>

            <div class="flex items-center justify-between py-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: var(--primary-light);">
                        <svg class="w-4 h-4" style="color: var(--primary)"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75
                                     0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21
                                     12.75 21a9.753 9.753 0 009.002-5.998z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm" style="color: var(--text)">Dark Mode</p>
                        <p class="text-xs mt-0.5" style="color: var(--muted)">
                            <span x-show="!saving">Applies to your account on any device</span>
                            <span x-show="saving" style="color: var(--primary)">Saving…</span>
                        </p>
                    </div>
                </div>

                <button @click="toggle()"
                        :class="dark ? 'bg-blue-600 border-blue-600' : 'border-slate-300'"
                        class="relative w-12 h-6 rounded-full border-2 transition-all duration-300
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        :style="!dark ? 'background: var(--bg3)' : ''">
                    <span :class="dark ? 'translate-x-6' : 'translate-x-0.5'"
                          class="absolute top-0.5 left-0 w-4 h-4 bg-white rounded-full shadow-md
                                 transition-transform duration-300 ease-in-out">
                    </span>
                </button>
            </div>
        </div>
    </div>
--}}