<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @else
        <style>
            html { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
            body { margin: 0; }
        </style>
    @endif
</head>
<body class="flex min-h-screen items-center justify-center bg-[#faf4ec] text-[#1b1b18]">
    <div class="w-full max-w-md rounded-[36px] border border-[#e6d9c5] bg-white p-8 shadow-[0_42px_80px_-40px_rgba(27,27,24,0.28)]">
        <div class="mb-8 text-center">
            <div class="mb-4 inline-flex items-center justify-center">
                @include('partials.logo', ['iconSize' => 32, 'gap' => 8, 'textClass' => 'text-headline-md'])
            </div>
            <h1 class="text-3xl font-semibold text-[#523923]">Admin sign in</h1>
            <p class="mt-2 text-sm text-[#5a4538]">Access your Heritage Basket backend dashboard.</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-[#e3b3a3] bg-[#fdecea] px-4 py-3 text-sm text-[#7c2d12]">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-medium text-[#5a4538]">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-3xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm text-[#1b1b18] outline-none transition focus:border-[#7c2d12]" placeholder="admin@example.com" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-[#5a4538]">Password</label>
                <input type="password" name="password" required class="w-full rounded-3xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm text-[#1b1b18] outline-none transition focus:border-[#7c2d12]" placeholder="••••••••" />
            </div>
            <label class="flex items-center gap-2 text-sm text-[#5a4538]">
                <input type="checkbox" name="remember" class="rounded border-[#d9c9ae]" />
                Remember me
            </label>
            <button type="submit" class="w-full rounded-3xl bg-[#7C2D12] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Sign in</button>
        </form>
    </div>
</body>
</html>
