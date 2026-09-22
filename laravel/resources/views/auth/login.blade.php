<x-layouts.app :title="'Log in - Celoe'" :showGuestNav="false">
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600">
                    <span class="text-lg font-bold text-white">C</span>
                </div>
                <h1 class="mt-4 text-2xl font-bold tracking-tight text-gray-900">
                    Sign in to Celoe
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Register here
                    </a>
                </p>
            </div>

            <div class="rounded-xl bg-white p-8 shadow-sm border border-gray-200">
                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="login" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Email address
                        </label>
                        <input
                            id="login"
                            name="login"
                            type="email"
                            value="{{ old('login') }}"
                            required
                            autocomplete="email"
                            autofocus
                            class="block w-full rounded-lg border {{ $errors->has('login') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors"
                            placeholder="you@example.com"
                        />
                        @error('login')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Password
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="block w-full rounded-lg border {{ $errors->has('password') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors"
                            placeholder="Enter your password"
                        />
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors"
                    >
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
