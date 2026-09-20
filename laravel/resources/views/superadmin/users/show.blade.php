<x-layouts.app :title="$user->name . ' - Celoe'">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">User detail view.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.users.edit', $user) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">Edit</a>
            <a href="{{ route('superadmin.users.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">Back to List</a>
        </div>
    </div>

    <div class="max-w-2xl">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-900">User Information</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $user->name }}</dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Username</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $user->username }}</dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $user->email }}</dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 sm:col-span-2">
                        <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700">{{ $user->role->name }}</span>
                    </dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Division</dt>
                    <dd class="mt-1 sm:col-span-2">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">{{ $user->division->name }}</span>
                    </dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $user->created_at->format('d M Y, H:i') }}</dd>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
