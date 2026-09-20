<x-layouts.app :title="'403 Forbidden - Celoe'">
    <div class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-50">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h1 class="mt-6 text-3xl font-bold text-gray-900">403</h1>
            <p class="mt-3 text-lg font-medium text-gray-700">Access Denied</p>
            <p class="mt-2 text-sm text-gray-500">
                Your role and division combination is not authorized to access this system.
                Please contact your administrator.
            </p>
            <div class="mt-6">
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                >
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
