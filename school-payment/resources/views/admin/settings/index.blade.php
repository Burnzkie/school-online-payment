{{--
    resources/views/partials/theme-toggle.blade.php
    ─────────────────────────────────────────────────
    Include this partial inside <head> BEFORE @vite():
        @include('partials.theme-toggle')

    Then add the toggle button HTML (below) anywhere in your header/nav.
    The button uses Alpine.js which is already loaded in each layout.
--}}

{{-- ① Flash-prevention script (must be first in <head>) --}}
<script>
    (function () {
        const saved = localStorage.getItem('sop-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (saved === 'dark' || (!saved && prefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

{{--
    ② Toggle BUTTON — paste this inside your header where you want the toggle.
    Requires Alpine.js (already loaded in your layouts).

    <div x-data="{
            dark: localStorage.getItem('sop-theme') === 'dark',
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('sop-theme', this.dark ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', this.dark);
            }
         }"
         class="flex items-center">
        <button @click="toggle()"
                class="relative w-9 h-9 flex items-center justify-center rounded-xl
                       text-slate-400 hover:text-slate-600 hover:bg-slate-100
                       dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-700
                       transition-all duration-200"
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

    ③ Settings page toggle (bigger, with label) — use on settings pages:

    <div x-data="{
            dark: localStorage.getItem('sop-theme') === 'dark',
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('sop-theme', this.dark ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', this.dark);
            }
         }"
         class="flex items-center justify-between p-4 rounded-2xl border"
         style="background: var(--bg3); border-color: var(--border);">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                 style="background: var(--primary-light);">
                <svg class="w-4 h-4" style="color: var(--primary)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75
                             0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21
                             12.75 21a9.753 9.753 0 009.002-5.998z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-sm" style="color: var(--text)">Dark Mode</p>
                <p class="text-xs" style="color: var(--muted)">Change background to dark/black</p>
            </div>
        </div>
        <button @click="toggle()"
                :class="dark ? 'bg-blue-600' : 'bg-slate-200'"
                class="relative w-12 h-6 rounded-full transition-colors duration-300 focus:outline-none">
            <span :class="dark ? 'translate-x-6' : 'translate-x-1'"
                  class="absolute top-0.5 left-0 w-5 h-5 bg-white rounded-full shadow
                         transition-transform duration-300 ease-in-out"></span>
        </button>
    </div>
--}}