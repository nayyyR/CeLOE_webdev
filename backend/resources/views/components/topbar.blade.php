@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-40 flex h-16 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6 lg:pl-72">
    {{-- Mobile menu button --}}
    <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" onclick="document.querySelector('.lg\\:hidden.sidebar-mobile').classList.toggle('hidden')">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <div class="flex flex-1"></div>
        <div class="flex items-center gap-x-4 lg:gap-x-6">
            {{-- Role Badge --}}
            <span class="hidden sm:inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                {{ $user->role->name ?? 'Unknown' }}
            </span>

            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

            {{-- User Info --}}
            <div class="flex items-center gap-x-4">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100">
                    <span class="text-sm font-medium text-gray-600">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
                </div>
                <div class="hidden sm:block">
                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->division->name ?? 'No division' }}</p>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
