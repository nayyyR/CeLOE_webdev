<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Celoe' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">

    @auth
        {{-- ============================================================ --}}
 {{-- AUTHENTICATED LAYOUT: Sidebar + Topbar + Content                --}}
 {{-- ============================================================ --}}
        <div class="min-h-full">
            {{-- Sidebar (desktop) --}}
            <x-sidebar />

            {{-- Mobile sidebar overlay --}}
            <div class="lg:hidden sidebar-mobile hidden fixed inset-0 z-50 bg-gray-900/80" onclick="this.classList.add('hidden')"></div>

            {{-- Mobile sidebar --}}
            <div class="lg:hidden sidebar-mobile hidden fixed inset-y-0 left-0 z-50 w-64 bg-gray-900">
                <x-sidebar />
            </div>

            {{-- Topbar --}}
            <x-topbar />

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="px-4 pt-4 sm:px-6 lg:pl-72">
                    <div class="rounded-md bg-green-50 p-4 border border-green-200">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="px-4 pt-4 sm:px-6 lg:pl-72">
                    <div class="rounded-md bg-red-50 p-4 border border-red-200">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Main Content --}}
            <main class="lg:pl-64">
                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    @else
        {{-- ============================================================ --}}
 {{-- GUEST LAYOUT: Simple centered auth pages                       --}}
 {{-- ============================================================ --}}
        <div class="min-h-full">
            {{-- Top Navigation Bar --}}
            <nav class="bg-white shadow-sm border-b border-gray-200">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600">
                                <span class="text-sm font-bold text-white">C</span>
                            </div>
                            <span class="text-lg font-semibold text-gray-900">Celoe</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                Log in
                            </a>
                            <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-3.5 py-1.5 text-sm font-medium text-white hover:bg-indigo-500 transition-colors">
                                Register
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="rounded-md bg-green-50 p-4 border border-green-200">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="rounded-md bg-red-50 p-4 border border-red-200">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>
    @endauth

</body>
</html>
