<x-layouts.app :title="'Edit Permission - Celoe'">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Permission</h1>
        <p class="mt-1 text-sm text-gray-600">Update permission details.</p>
    </div>

    <div class="max-w-lg">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('superadmin.permissions.update', $permission) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Permission Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $permission->name) }}" required
                        class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description <span class="text-gray-400">(optional)</span></label>
                    <input id="description" name="description" type="text" value="{{ old('description', $permission->description) }}"
                        class="block w-full rounded-lg border {{ $errors->has('description') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Update Permission</button>
                    <a href="{{ route('superadmin.permissions.show', $permission) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
