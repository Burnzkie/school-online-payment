<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password – PAC Online Payment</title>
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
                Create New<br>Password.
            </p>
            <p class="text-lg text-slate-300 opacity-90">
                Choose a strong password for your account.
            </p>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-lg">

            <!-- Reset Password Form -->
            <div class="glass rounded-3xl p-8 lg:p-10 shadow-2xl">
                <div class="text-center mb-8">
                    <i class="fas fa-key text-5xl text-blue-500 mb-4"></i>
                    <h2 class="text-3xl font-bold">Reset Password</h2>
                    <p class="text-slate-400 mt-3 text-sm">
                        Enter your new password below.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                                   class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 transition"
                                   placeholder="your.email@example.com">
                            @error('email')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">New Password</label>
                            <div class="relative">
                                <input type="password" name="password" required
                                       class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 transition"
                                       placeholder="Enter new password">
                                <button type="button" onclick="togglePassword(this)" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Confirm Password</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" required
                                       class="w-full bg-slate-800 border border-slate-600 rounded-2xl px-6 py-4 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 transition"
                                       placeholder="Confirm new password">
                                <button type="button" onclick="togglePassword(this)" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 py-5 rounded-3xl font-semibold text-lg transition-all active:scale-[0.98]">
                            <i class="fas fa-check-circle mr-2"></i>
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        function togglePassword(btn) {
            const input = btn.parentElement.querySelector('input');
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }
    </script>

</body>
</html>