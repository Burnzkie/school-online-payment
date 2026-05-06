{{--
    resources/views/partials/darkmode.blade.php
    ─────────────────────────────────────────────
    Include this in the <head> of EVERY layout, right before @vite():

        @include('partials.darkmode')
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    It does two things:
    1. Instantly applies dark class from DB before CSS loads (zero flicker)
    2. Defines window.toggleDark() used by the toggle button in each layout
--}}

{{-- ① Instant dark class — runs before CSS, prevents white flash --}}
<script>
    (function () {
        var dark = {{ auth()->user()?->dark_mode ? 'true' : 'false' }};
        if (dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>