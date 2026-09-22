<x-layouts.app :title="'Create User - Celoe'">
    @php
        $divisionsJson = $divisions->map(fn($d) => ['id' => $d->id, 'name' => $d->name])->toJson();
        $generalDivision = $divisions->firstWhere('name', 'general');
    @endphp

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create User</h1>
        <p class="mt-1 text-sm text-gray-600">Add a new user to the system.</p>
    </div>

    <div class="max-w-2xl">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('superadmin.users.store') }}" class="space-y-5" id="createForm">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="John Doe" />
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required
                        class="block w-full rounded-lg border {{ $errors->has('username') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="johndoe" />
                    @error('username') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="block w-full rounded-lg border {{ $errors->has('email') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="you@example.com" />
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input id="password" name="password" type="password" required
                        class="block w-full rounded-lg border {{ $errors->has('password') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Min. 8 characters" />
                    @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                    <select id="role_id" name="role_id" required
                        class="block w-full rounded-lg border {{ $errors->has('role_id') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select a role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="division_id" class="block text-sm font-medium text-gray-700 mb-1.5">Division</label>
                    <select id="division_id" name="division_id" required
                        class="block w-full rounded-lg border {{ $errors->has('division_id') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select a division</option>
                    </select>
                    @error('division_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Create User</button>
                    <a href="{{ route('superadmin.users.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const allDivisions = {!! $divisionsJson !!};
            const generalId = {{ $generalDivision->id ?? 'null' }};
            const roleSelect = document.getElementById('role_id');
            const divisionSelect = document.getElementById('division_id');

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
                const selectedRoleId = roleSelect.value;
                const roleOption = roleSelect.options[roleSelect.selectedIndex];
                const roleName = roleOption ? roleOption.text.toLowerCase() : '';
                const currentVal = divisionSelect.value;

                const allowed = getAllowedDivisions(roleName);

                divisionSelect.innerHTML = '<option value="">Select a division</option>';
                allowed.forEach(function(d) {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.name;
                    if (d.id == currentVal) opt.selected = true;
                    divisionSelect.appendChild(opt);
                });

                if (allowed.length === 1) {
                    divisionSelect.value = allowed[0].id;
                }
            }

            roleSelect.addEventListener('change', updateDivisions);
            updateDivisions();
        })();
    </script>
</x-layouts.app>
