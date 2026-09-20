<x-layouts.app :title="$ticket->ticket_number . ' - Celoe'">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->ticket_number }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $ticket->subject }}</p>
        </div>
        <a href="{{ route('superadmin.tickets.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">Back to List</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Threads --}}
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-900">Conversation</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($threads as $thread)
                        <div class="px-6 py-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50">
                                    <span class="text-sm font-medium text-indigo-600">{{ strtoupper(substr(($users->get($thread->user_id)->name ?? 'U'), 0, 1)) }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $users->get($thread->user_id)->name ?? 'Unknown' }}</span>
                                    <span class="ml-2 text-xs text-gray-500">{{ $thread->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                            <div class="ml-11 text-sm text-gray-700 whitespace-pre-wrap">{{ $thread->body }}</div>
                            @if (!empty($thread->attachments))
                                <div class="ml-11 mt-2 flex flex-wrap gap-2">
                                    @foreach ($thread->attachments as $attachment)
                                        <a href="{{ Storage::disk($attachment['disk'])->url($attachment['path']) }}" target="_blank"
                                            class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200 transition-colors">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                            {{ $attachment['original_name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-sm text-gray-500">No replies yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Reply Form --}}
            @if ($ticket->status !== 'closed')
                <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h2 class="text-sm font-semibold text-gray-900">Reply</h2>
                    </div>
                    <form method="POST" action="{{ route('superadmin.tickets.reply', $ticket) }}" enctype="multipart/form-data" class="p-6">
                        @csrf
                        <div>
                            <textarea name="body" rows="3" required placeholder="Write your reply..."
                                class="block w-full rounded-lg border {{ $errors->has('body') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('body') }}</textarea>
                            @error('body') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="mt-3">
                            <label for="reply_attachments" class="block text-xs font-medium text-gray-500 mb-1">Attachments (optional)</label>
                            <input id="reply_attachments" name="attachments[]" type="file" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx"
                                class="block w-full rounded-lg border border-gray-300 px-3.5 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Send Reply</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status --}}
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-900">Status</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        @if ($ticket->status === 'open')
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">Open</span>
                        @elseif ($ticket->status === 'in_progress')
                            <span class="inline-flex items-center rounded-md bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-700">In Progress</span>
                        @elseif ($ticket->status === 'resolved')
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Resolved</span>
                        @elseif ($ticket->status === 'closed')
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Closed</span>
                        @endif
                    </div>

                    @if ($ticket->status !== 'closed')
                        <div class="space-y-2">
                            @if ($ticket->status === 'in_progress')
                                <form method="POST" action="{{ route('superadmin.tickets.status', $ticket) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="resolved">
                                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors">
                                        Mark Resolved
                                    </button>
                                </form>
                            @endif
                            @if ($ticket->status === 'resolved')
                                <form method="POST" action="{{ route('superadmin.tickets.status', $ticket) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="closed">
                                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 transition-colors">
                                        Close Ticket
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assign (only for open tickets) --}}
            @if ($ticket->status === 'open' && $employees->isNotEmpty())
                <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h2 class="text-sm font-semibold text-gray-900">Assign Ticket</h2>
                    </div>
                    <form method="POST" action="{{ route('superadmin.tickets.assign', $ticket) }}" class="p-6">
                        @csrf
                        <div>
                            <label for="assigned_employee_id" class="block text-sm font-medium text-gray-700 mb-1.5">Employee</label>
                            <select id="assigned_employee_id" name="assigned_employee_id" required
                                class="block w-full rounded-lg border {{ $errors->has('assigned_employee_id') ? 'border-red-300 ring-red-300' : 'border-gray-300' }} px-3.5 py-2.5 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select an employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $ticket->assigned_employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} (Active: {{ $employee->active_count }} | Completed: {{ $employee->completed_count }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_employee_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('ticket') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
                                Assign
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Details --}}
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-900">Details</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="px-6 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-xs font-medium text-gray-500">Division</dt>
                        <dd class="mt-1 sm:col-span-2">
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $ticket->targetDivision->name }}</span>
                        </dd>
                    </div>
                    <div class="px-6 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-xs font-medium text-gray-500">Requester</dt>
                        <dd class="mt-1 text-xs text-gray-900 sm:col-span-2">{{ $ticket->creator->name ?? 'Unknown' }}</dd>
                    </div>
                    <div class="px-6 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-xs font-medium text-gray-500">Assigned To</dt>
                        <dd class="mt-1 text-xs text-gray-900 sm:col-span-2">{{ $ticket->assignedEmployee->name ?? 'Unassigned' }}</dd>
                    </div>
                    <div class="px-6 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-xs font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-xs text-gray-900 sm:col-span-2">{{ $ticket->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                    @if ($ticket->resolved_at)
                        <div class="px-6 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-xs font-medium text-gray-500">Resolved</dt>
                            <dd class="mt-1 text-xs text-gray-900 sm:col-span-2">{{ $ticket->resolved_at->format('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                    @if ($ticket->closed_at)
                        <div class="px-6 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-xs font-medium text-gray-500">Closed</dt>
                            <dd class="mt-1 text-xs text-gray-900 sm:col-span-2">{{ $ticket->closed_at->format('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Activity Log --}}
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-900">Activity Log</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @forelse ($activityLogs as $log)
                            <div class="flex items-start gap-3">
                                <div class="mt-1 h-2 w-2 shrink-0 rounded-full bg-indigo-400"></div>
                                <div>
                                    <p class="text-xs text-gray-700">
                                        <span class="font-medium">{{ $users->get($log->user_id)->name ?? 'System' }}</span>
                                        {{ str_replace('_', ' ', $log->action) }}
                                        @if ($log->old_status && $log->new_status)
                                            from <span class="font-medium">{{ $log->old_status }}</span> to <span class="font-medium">{{ $log->new_status }}</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $log->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500">No activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
