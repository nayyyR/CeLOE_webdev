<x-layouts.app :title="'Users - Celoe'">
    @php
        $divisionsJson = $divisions->map(fn($d) => ['id' => $d->id, 'name' => $d->name])->toJson();
    @endphp

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Users</h1>
            <p class="mt-1 text-sm text-gray-600">Manage all system users.</p>
        </div>
        <a href="{{ route('superadmin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add User
        </a>
    </div>

    {{-- Search & Filters --}}
    <div class="mb-6 rounded-xl bg-white p-4 shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, username, or email..."
                    class="block w-full rounded-lg border border-gray-300 px-3.5 py-2 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
            <div class="flex gap-3">
                <select name="role_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                <select name="division_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Divisions</option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors">Filter</button>
                @if (request('search') || request('role_id') || request('division_id'))
                    <a href="{{ route('superadmin.users.index') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Division</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50">
                                        <span class="text-sm font-medium text-indigo-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $user->username }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700">{{ $user->role->name }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">{{ $user->division->name }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.users.show', $user) }}" class="rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">View</a>
                                    <a href="{{ route('superadmin.users.edit', $user) }}" class="rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 border border-red-200 hover:bg-red-100 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="border-t border-gray-200 px-6 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <script>
        (function() {
            const allDivisions = {!! $divisionsJson !!};
            const currentRoleId = '{{ request("role_id") }}';
            const currentDivisionId = '{{ request("division_id") }}';
            const roleSelect = document.querySelector('select[name="role_id"]');
            const divisionSelect = document.querySelector('select[name="division_id"]');

            function getAllowedDivisions(roleName) {
                if (roleName === 'user' || roleName === 'admin' || roleName === 'super admin') {
                    return allDivisions.filter(d => d.name.toLowerCase() === 'general');
                }
                if (roleName === 'employee') {
                    return allDivisions.filter(d => d.name.toLowerCase() !== 'general');
                }
                return allDivisions;
            }

            function updateDivisions() {
                const roleOption = roleSelect.options[roleSelect.selectedIndex];
                const roleName = roleOption ? roleOption.text.toLowerCase() : '';

                const allowed = getAllowedDivisions(roleName);

                divisionSelect.innerHTML = '<option value="">All Divisions</option>';
                allowed.forEach(function(d) {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.name;
                    divisionSelect.appendChild(opt);
                });

                if (currentDivisionId && allowed.some(d => d.id == currentDivisionId)) {
                    divisionSelect.value = currentDivisionId;
                }
            }

            roleSelect.addEventListener('change', updateDivisions);
            updateDivisions();
        })();
    </script>
</x-layouts.app>
