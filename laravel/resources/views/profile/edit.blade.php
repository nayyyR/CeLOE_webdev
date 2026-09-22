<x-layouts.app :title="'My Profile - Celoe'">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <p class="mt-1 text-sm text-gray-600">Manage your account settings.</p>
    </div>

    <div class="max-w-2xl space-y-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-900">Profile Information</h2>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                            class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}"
                            class="block w-full rounded-lg border {{ $errors->has('email') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                        <input type="text" disabled value="{{ $user->username }}"
                            class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-gray-500 sm:text-sm cursor-not-allowed" />
                        <p class="mt-1 text-xs text-gray-500">Username cannot be changed.</p>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Save Changes</button>
                </div>
            </form>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-900">Change Password</h2>
            </div>
            <form method="POST" action="{{ route('profile.password.update') }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1.5">Current Password</label>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                            class="block w-full rounded-lg border {{ $errors->has('current_password') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        @error('current_password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                            class="block w-full rounded-lg border {{ $errors->has('password') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Min. 8 characters" />
                        @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                            class="block w-full rounded-lg border {{ $errors->has('password_confirmation') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Repeat your password" />
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Update Password</button>
                </div>
            </form>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-900">Account Information</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-6 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 sm:col-span-2">
                        <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $user->role->name ?? 'Unknown' }}</span>
                    </dd>
                </div>
                <div class="px-6 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Division</dt>
                    <dd class="mt-1 sm:col-span-2">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $user->division->name ?? 'Unknown' }}</span>
                    </dd>
                </div>
                <div class="px-6 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $user->created_at->format('d M Y') }}</dd>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
