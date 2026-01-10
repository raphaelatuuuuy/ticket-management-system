<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Support Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filter -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="text" id="searchInput" placeholder="Search tickets..." 
                           class="flex-1 rounded-md border-gray-300" onkeyup="filterTickets()">
                    <select id="statusFilter" class="rounded-md border-gray-300" onchange="filterTickets()">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ strtolower($status->description) }}">{{ $status->description }}</option>
                        @endforeach
                    </select>
                    <select id="assigneeFilter" class="rounded-md border-gray-300" onchange="filterTickets()">
                        <option value="">All Assignees</option>
                        <option value="unassigned">Unassigned</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }} ({{ ucfirst($agent->role) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-fixed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">ID</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Customer</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Priority</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">State</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Assigned</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Updated</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTableBody" class="bg-white divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            <tr class="ticket-row hover:bg-gray-50" 
                                data-title="{{ $ticket->title }}"
                                data-status="{{ $ticket->status_relation ? strtolower($ticket->status_relation->description) : '' }}"
                                data-assignee="{{ $ticket->assigned_to }}"
                                style="background-color: {{ $ticket->priority_relation ? $ticket->priority_relation->color : '#ffffff' }}08;">
                                <td class="px-3 py-2 whitespace-nowrap text-xs">#{{ $ticket->ticket_number }}</td>
                                <td class="px-3 py-2">
                                    <div class="text-xs font-medium text-gray-900 truncate" title="{{ $ticket->title }}">{{ Str::limit($ticket->title, 30) }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs truncate" title="{{ $ticket->user ? $ticket->user->name : 'N/A' }}">{{ $ticket->user ? $ticket->user->name : 'N/A' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">
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
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if($ticket->deleted_at || ($ticket->user && $ticket->user->deleted_at))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Deactivate
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs truncate" title="{{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned' }}">
                                    {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                    {{ $ticket->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs">
                                    <div class="flex items-center gap-1 min-w-max">
                                        @if(!$ticket->deleted_at && (!$ticket->user || !$ticket->user->deleted_at))
                                            <button onclick="openTicketDetail({{ $ticket->id }})" 
                                                    class="text-indigo-600 hover:text-indigo-900 p-0.5 hover:bg-indigo-50 rounded flex-shrink-0"
                                                    title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            @if($ticket->assigned_to)
                                                <button onclick="openAssignModal({{ $ticket->id }})" 
                                                        class="text-blue-600 hover:text-blue-900 p-0.5 hover:bg-blue-50 rounded flex-shrink-0"
                                                        title="Edit Assignment">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            @elseif(!$ticket->assigned_to || $ticket->escalation_requested)
                                                <button onclick="openAssignModal({{ $ticket->id }})" 
                                                        class="text-green-600 hover:text-green-900 p-0.5 hover:bg-green-50 rounded flex-shrink-0"
                                                        title="{{ $ticket->escalation_requested ? 'Escalate' : 'Assign' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                            <button onclick="confirmDeleteTicket({{ $ticket->id }}, '#{{ $ticket->ticket_number }}')" 
                                                    class="text-red-600 hover:text-red-900 p-0.5 hover:bg-red-50 rounded flex-shrink-0"
                                                    title="Deactivate Ticket">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </button>
                                        @elseif($ticket->deleted_at || ($ticket->user && $ticket->user->deleted_at))
                                            <button onclick="confirmRestoreTicket({{ $ticket->id }}, '#{{ $ticket->ticket_number }}', {{ $ticket->user && $ticket->user->deleted_at ? 'true' : 'false' }}, {{ $ticket->user ? json_encode(['name' => $ticket->user->name, 'email' => $ticket->user->email, 'role' => ucfirst($ticket->user->role)]) : 'null' }})" 
                                                    class="text-green-600 hover:text-green-900 p-0.5 hover:bg-green-50 rounded flex-shrink-0"
                                                    title="Restore Ticket">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">No tickets found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Deactivate Ticket</h3>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Are you sure you want to deactivate ticket <span id="deleteTicketNumber" class="font-semibold"></span>? You can restore it later.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium">Cancel</button>
                    <button type="button" onclick="deleteTicket()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium">Deactivate</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Restore Confirmation Modal -->
    <div id="restoreModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Restore Ticket</h3>
                <p class="text-sm text-gray-600 text-center mb-2">
                    Are you sure you want to restore ticket <span id="restoreTicketNumber" class="font-semibold"></span>?
                </p>
                
                <!-- Customer Details Section (shown when user is deactivated) -->
                <div id="customerDetailsSection" class="hidden mb-4">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mt-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-amber-800 mb-2">Customer Account is Deactivated</p>
                                <div class="text-xs text-gray-700 space-y-1">
                                    <p><span class="font-medium">Name:</span> <span id="customerName"></span></p>
                                    <p><span class="font-medium">Email:</span> <span id="customerEmail"></span></p>
                                    <p><span class="font-medium">Role:</span> <span id="customerRole"></span></p>
                                </div>
                                <p class="text-xs text-amber-700 mt-3">
                                    This customer account will be automatically activated when you restore this ticket.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeRestoreModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium">Cancel</button>
                    <button type="button" onclick="restoreTicket()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                        <span id="restoreButtonText">Restore</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Modal with Ticket Details -->
    <div id="assignModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Assign & Configure Ticket</h3>
                <button type="button" onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Ticket Details Section -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Ticket Details</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Ticket ID</label>
                            <p id="modalTicketId" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Customer</label>
                            <p id="modalCustomer" class="text-sm text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Current Status</label>
                            <p id="modalStatus" class="text-sm"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Created</label>
                            <p id="modalCreated" class="text-sm text-gray-900"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-500 uppercase">Subject</label>
                            <p id="modalTitle" class="text-sm text-gray-900 font-medium"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-500 uppercase">Description</label>
                            <p id="modalDescription" class="text-sm text-gray-700 whitespace-pre-wrap max-h-32 overflow-y-auto"></p>
                        </div>
                    </div>
                </div>

                <!-- Assignment Form -->
                <form id="assignForm">
                    @csrf
                    <input type="hidden" id="assignTicketId">
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-600">*</span></label>
                            <select id="assignStatus" required class="w-full rounded-md border-gray-300">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assign To <span class="text-red-600">*</span></label>
                            <select id="assignTo" required class="w-full rounded-md border-gray-300">
                                <option value="">Select Agent...</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }} ({{ ucfirst($agent->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select id="assignPriority" class="w-full rounded-md border-gray-300">
                                <option value="">Select Priority...</option>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority->id }}">{{ $priority->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select id="assignCategory" class="w-full rounded-md border-gray-300">
                                <option value="">Select Category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeAssignModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium">Assign & Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ticket Detail Panel -->
    @include('admin.tickets.detail-panel')

    <script>
        // Make statuses available globally for the detail panel
        window.availableStatuses = @json($statuses);

        function openAssignModal(ticketId) {
            document.getElementById('assignTicketId').value = ticketId;
            document.getElementById('assignModal').classList.remove('hidden');
            
            // Load current ticket details from server
            fetch(`/tickets/${ticketId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Populate ticket details section
                document.getElementById('modalTicketId').textContent = '#' + data.ticket_number;
                document.getElementById('modalCustomer').textContent = data.customer_name || 'N/A';
                document.getElementById('modalTitle').textContent = data.title;
                document.getElementById('modalDescription').textContent = data.description;
                document.getElementById('modalCreated').textContent = data.created_at_formatted;
                document.getElementById('modalStatus').innerHTML = `
                    <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                          style="background-color: ${data.status_color}20; color: ${data.status_color}">
                        ${data.status}
                    </span>
                `;
                
                // Populate form fields with current values
                document.getElementById('assignStatus').value = data.status_id || '';
                document.getElementById('assignPriority').value = data.priority_id || '';
                document.getElementById('assignCategory').value = data.category_id || '';
                
                // Set assigned agent - need to get the agent ID from the response
                const assignToSelect = document.getElementById('assignTo');
                if (data.assigned_to_id) {
                    assignToSelect.value = data.assigned_to_id;
                } else {
                    assignToSelect.value = '';
                }
            })
            .catch(error => console.error('Error loading ticket details:', error));
        }

        function closeAssignModal() {
            document.getElementById('assignModal').classList.add('hidden');
        }

        document.getElementById('assignForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const ticketId = document.getElementById('assignTicketId').value;
            const assignTo = document.getElementById('assignTo').value;
            const priority = document.getElementById('assignPriority').value;
            const category = document.getElementById('assignCategory').value;
            const statusId = document.getElementById('assignStatus').value;

            // First assign the ticket
            fetch(`/admin/tickets/${ticketId}/assign`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    assigned_to: assignTo,
                    priority_id: priority,
                    category_id: category
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Then update status if changed
                    return fetch(`/admin/tickets/${ticketId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status_id: statusId })
                    });
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAssignModal();
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error assigning ticket. Please try again.');
            });
        });

        function filterTickets() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const statusValue = document.getElementById('statusFilter').value.toLowerCase();
            const assigneeValue = document.getElementById('assigneeFilter').value;
            const rows = document.querySelectorAll('.ticket-row');
            
            rows.forEach(row => {
                const title = row.dataset.title.toLowerCase();
                const status = row.dataset.status.toLowerCase();
                const assignee = row.dataset.assignee;
                
                const matchesSearch = title.includes(searchValue);
                const matchesStatus = !statusValue || status === statusValue;
                const matchesAssignee = !assigneeValue || 
                    (assigneeValue === 'unassigned' && !assignee) || 
                    assignee === assigneeValue;
                
                row.style.display = (matchesSearch && matchesStatus && matchesAssignee) ? '' : 'none';
            });
        }

        // Delete ticket functions
        let ticketToDelete = null;

        function confirmDeleteTicket(ticketId, ticketNumber) {
            ticketToDelete = ticketId;
            document.getElementById('deleteTicketNumber').textContent = ticketNumber;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            ticketToDelete = null;
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function deleteTicket() {
            if (!ticketToDelete) return;

            fetch(`/admin/tickets/${ticketToDelete}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDeleteModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Error deactivating ticket');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deactivating ticket. Please try again.');
            });
        }
        
        // Restore ticket functions
        let ticketToRestore = null;

        function confirmRestoreTicket(ticketId, ticketNumber, userDeactivated = false, userData = null) {
            ticketToRestore = ticketId;
            document.getElementById('restoreTicketNumber').textContent = ticketNumber;
            
            // Show/hide customer details section based on user account status
            const customerDetailsSection = document.getElementById('customerDetailsSection');
            const restoreButtonText = document.getElementById('restoreButtonText');
            
            if (userDeactivated && userData) {
                // Show customer details
                customerDetailsSection.classList.remove('hidden');
                document.getElementById('customerName').textContent = userData.name;
                document.getElementById('customerEmail').textContent = userData.email;
                document.getElementById('customerRole').textContent = userData.role;
                restoreButtonText.textContent = 'Activate & Restore';
            } else {
                // Hide customer details
                customerDetailsSection.classList.add('hidden');
                restoreButtonText.textContent = 'Restore';
            }
            
            document.getElementById('restoreModal').classList.remove('hidden');
        }

        function closeRestoreModal() {
            ticketToRestore = null;
            document.getElementById('restoreModal').classList.add('hidden');
        }

        function restoreTicket() {
            if (!ticketToRestore) return;

            fetch(`/admin/tickets/${ticketToRestore}/restore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeRestoreModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Error restoring ticket');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error restoring ticket. Please try again.');
            });
        }
    </script>

</x-app-layout>
