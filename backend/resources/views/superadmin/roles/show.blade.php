<x-layouts.app :title="$role->name . ' - Celoe'">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $role->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">Role details and assigned permissions.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.roles.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">Back to List</a>
        </div>
    </div>

    <div class="max-w-2xl space-y-6">
        {{-- Role Info --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-900">Role Information</h2>
            </div>
            <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500">Name</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $role->name }}</dd>
            </div>
        </div>

        {{-- Assigned Permissions (read-only) --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-900">Assigned Permissions</h2>
            </div>
            <div class="p-6">
                @forelse ($role->permissions as $permission)
                    <span class="inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 mr-2 mb-2">{{ $permission->name }}</span>
                @empty
                    <p class="text-sm text-gray-500">No permissions assigned to this role.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
