<x-layouts.app :title="'Register - Celoe'" :showGuestNav="false">
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600">
                    <span class="text-lg font-bold text-white">C</span>
                </div>
                <h1 class="mt-4 text-2xl font-bold tracking-tight text-gray-900">
                    Create your account
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Sign in here
                    </a>
                </p>
            </div>

            <div class="rounded-xl bg-white p-8 shadow-sm border border-gray-200">
                <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Full name
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            autocomplete="name"
                            autofocus
                            class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors"
                            placeholder="John Doe"
                        />
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Email address
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            class="block w-full rounded-lg border {{ $errors->has('email') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors"
                            placeholder="you@example.com"
                        />
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Password
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="block w-full rounded-lg border {{ $errors->has('password') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors"
                            placeholder="Min. 8 characters"
                        />
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Confirm password
                        </label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors"
                            placeholder="Repeat your password"
                        />
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors"
                    >
                        Create account
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
