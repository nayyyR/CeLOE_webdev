<x-layouts.app :title="'Dashboard - Celoe'">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Super Admin Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Welcome back, {{ auth()->user()->name }}.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-200 sm:col-span-2 lg:col-span-3">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-50">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ auth()->user()->role->name }} &middot;
                        {{ auth()->user()->division->name }} Division
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
