<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assigned Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                <div class="flex gap-4">
                    <input type="text" id="searchInput" placeholder="Search tickets..." 
                           class="flex-1 rounded-md border-gray-300" onkeyup="filterTickets()">
                    <select id="statusFilter" class="rounded-md border-gray-300" onchange="filterTickets()">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ strtolower($status->description) }}">{{ $status->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Updated</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTableBody" class="bg-white divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            <tr class="ticket-row hover:bg-gray-50" 
                                data-title="{{ $ticket->title }}"
                                data-status="{{ $ticket->status_relation ? strtolower($ticket->status_relation->description) : '' }}"
                                @if($ticket->priority_relation)
                                    style="background-color: {{ $ticket->priority_relation->color }}08;"
                                @endif>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">#{{ $ticket->ticket_number }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->title, 40) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                          style="background-color: {{ $ticket->status_relation ? $ticket->status_relation->color : '#6b7280' }}20; color: {{ $ticket->status_relation ? $ticket->status_relation->color : '#6b7280' }}">
                                        {{ $ticket->status_relation ? $ticket->status_relation->description : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->priority_relation)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                              style="background-color: {{ $ticket->priority_relation->color }}20; color: {{ $ticket->priority_relation->color }}">
                                            {{ $ticket->priority_relation->description }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ticket->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <button onclick="openTicketDetail({{ $ticket->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900 p-1 hover:bg-indigo-50 rounded"
                                                title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">No assigned tickets found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('agent.tickets.detail-panel')

    <script>
        window.availableStatuses = @json($statuses);

        function filterTickets() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const statusValue = document.getElementById('statusFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.ticket-row');
            
            rows.forEach(row => {
                const title = row.dataset.title.toLowerCase();
                const status = row.dataset.status.toLowerCase();
                
                const matchesSearch = title.includes(searchValue);
                const matchesStatus = !statusValue || status === statusValue;
                
                row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });
        }
    </script>
</x-app-layout>
