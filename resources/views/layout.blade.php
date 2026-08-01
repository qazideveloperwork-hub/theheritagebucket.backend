<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Admin')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @else
        <style>
            html { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
            body { margin: 0; }
        </style>
    @endif
</head>
<body class="min-h-screen bg-[#F4F1EC] text-[#1C1A17]">
    <div class="min-h-screen bg-[#f8f4ee] text-[#1b1b18]">
        <header class="sticky top-0 z-20 border-b border-[#d9c9ae] bg-white/80 backdrop-blur-sm">
            <div class="mx-auto flex max-w-[1400px] flex-col gap-4 px-4 py-4 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    @include('partials.logo', ['iconSize' => 28, 'gap' => 6, 'textClass' => 'text-headline-sm'])
                </a>
                <nav class="flex flex-wrap items-center gap-3 text-sm text-[#5A4538]">
                    <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 hover:bg-[#f1e5d5]">Dashboard</a>
                    @auth
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-full border border-[#d9c9ae] px-4 py-2 hover:bg-[#f1e5d5]">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full border border-[#d9c9ae] px-4 py-2 hover:bg-[#f1e5d5]">Login</a>
                    @endauth
                </nav>
            </div>
        </header>

        <div class="flex items-start">
            @include('admin._sidebar')

            <main class="min-w-0 flex-1 p-4 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @include('partials.confirm-modal')
</body>
</html>
