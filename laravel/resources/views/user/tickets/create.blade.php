<x-layouts.app :title="'Create Ticket - Celoe'">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create Ticket</h1>
        <p class="mt-1 text-sm text-gray-600">Submit a new support request.</p>
    </div>

    <div class="max-w-2xl">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('user.tickets.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1.5">Subject</label>
                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required autofocus
                        class="block w-full rounded-lg border {{ $errors->has('subject') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Brief description of your issue" />
                    @error('subject') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="target_division_id" class="block text-sm font-medium text-gray-700 mb-1.5">Target Division</label>
                    <select id="target_division_id" name="target_division_id" required
                        class="block w-full rounded-lg border {{ $errors->has('target_division_id') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select a division</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('target_division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @error('target_division_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <textarea id="description" name="description" rows="5" required
                        class="block w-full rounded-lg border {{ $errors->has('description') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Describe your issue in detail...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="attachments" class="block text-sm font-medium text-gray-700 mb-1.5">Attachments <span class="text-gray-400">(optional, max 5 files, 2MB each)</span></label>
                    <input id="attachments" name="attachments[]" type="file" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx"
                        class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    @error('attachments') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    @error('attachments.*') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Submit Ticket</button>
                    <a href="{{ route('user.tickets.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
