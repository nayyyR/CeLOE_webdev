<x-layouts.app :title="$division->name . ' - Celoe'">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $division->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">Division detail view.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.divisions.edit', $division) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">Edit</a>
            <a href="{{ route('superadmin.divisions.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">Back to List</a>
        </div>
    </div>

    <div class="max-w-2xl">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-900">Division Information</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $division->name }}</dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Users</dt>
                    <dd class="mt-1 sm:col-span-2">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">{{ $division->users_count }} users</span>
                    </dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Tickets</dt>
                    <dd class="mt-1 sm:col-span-2">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">{{ $division->tickets_count }} tickets</span>
                    </dd>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
