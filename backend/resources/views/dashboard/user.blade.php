<x-layouts.app :title="'Dashboard - Celoe'">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">My Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Welcome back, {{ auth()->user()->name }}.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {{-- Info Card --}}
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-200 sm:col-span-2 lg:col-span-3">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-50">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ auth()->user()->role->name }} &middot;
                        {{ auth()->user()->division->name }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
