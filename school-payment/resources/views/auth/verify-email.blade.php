<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email – PAC Online Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-slate-100 min-h-screen flex">

    <!-- Left illustration panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-cover bg-center relative"
         style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80')">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/70 to-transparent"></div>
        <div class="absolute bottom-16 left-12 max-w-md">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-4xl shadow-lg">🎓</div>
                <h1 class="text-5xl font-extrabold tracking-tight">PAC</h1>
            </div>
            <p class="text-4xl font-bold leading-tight mb-6">
                Verify Your<br>Email Address.
            </p>
            <p class="text-lg text-slate-300 opacity-90">
                One more step to secure your account.
            </p>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-lg">

            <!-- Verify Email Form -->
            <div class="glass rounded-3xl p-8 lg:p-10 shadow-2xl">
                <div class="text-center mb-8">
                    <i class="fas fa-envelope-circle-check text-5xl text-blue-500 mb-4"></i>
                    <h2 class="text-3xl font-bold">Verify Your Email</h2>
                    <p class="text-slate-400 mt-3 text-sm">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
                    </p>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="bg-green-900/30 border border-green-600 text-green-200 px-6 py-4 rounded-2xl mb-6 text-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                <div class="space-y-4">
                    <!-- Resend Verification Email -->
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 py-5 rounded-3xl font-semibold text-lg transition-all active:scale-[0.98]">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Resend Verification Email
                        </button>
                    </form>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full bg-slate-700 hover:bg-slate-600 py-4 rounded-3xl font-semibold transition-all active:scale-[0.98] text-slate-300">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Log Out
                        </button>
                    </form>
                </div>

                <div class="mt-8 p-4 bg-blue-900/20 border border-blue-700/50 rounded-2xl">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                        <div class="text-sm text-slate-300">
                            <strong class="text-blue-400">Didn't receive the email?</strong>
                            <p class="mt-1">Check your spam folder or click the button above to resend.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>