<x-layouts.app :title="'Assigned Tickets - Celoe'">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Assigned Tickets</h1>
        <p class="mt-1 text-sm text-gray-600">Tickets assigned to you in the {{ auth()->user()->division->name }} division.</p>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Requester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $ticket->ticket_number }}</div>
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ $ticket->subject }}</div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $ticket->creator->name ?? 'Unknown' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($ticket->status === 'open')
                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">Open</span>
                                @elseif ($ticket->status === 'in_progress')
                                    <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700">In Progress</span>
                                @elseif ($ticket->status === 'resolved')
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700">Resolved</span>
                                @elseif ($ticket->status === 'closed')
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">Closed</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $ticket->created_at->format('d M Y') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a href="{{ route('employee.tickets.show', $ticket) }}" class="rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No tickets assigned to you yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->hasPages())
            <div class="border-t border-gray-200 px-6 py-3">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
