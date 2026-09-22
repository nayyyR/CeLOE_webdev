<x-layouts.app :title="'Divisions - Celoe'">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Divisions</h1>
            <p class="mt-1 text-sm text-gray-600">Manage organizational divisions.</p>
        </div>
        <a href="{{ route('superadmin.divisions.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Division
        </a>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tickets</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($divisions as $division)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $division->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">{{ $division->users_count }} users</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">{{ $division->tickets_count }} tickets</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.divisions.show', $division) }}" class="rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">View</a>
                                    <a href="{{ route('superadmin.divisions.edit', $division) }}" class="rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('superadmin.divisions.destroy', $division) }}" onsubmit="return confirm('Delete this division?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 border border-red-200 hover:bg-red-100 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No divisions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($divisions->hasPages())
            <div class="border-t border-gray-200 px-6 py-3">
                {{ $divisions->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
