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
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Updated</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTableBody" class="bg-white divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            <tr class="ticket-row hover:bg-gray-50" 
                                data-title="{{ $ticket->title }}"
                                data-status="{{ $ticket->status_relation ? strtolower($ticket->status_relation->description) : '' }}"
                                data-assignee="{{ $ticket->assigned_to }}"
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned' }}
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        @if($ticket->assigned_to)
                                            <button onclick="openAssignModal({{ $ticket->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 p-1 hover:bg-blue-50 rounded"
                                                    title="Edit Assignment">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                        @elseif(!$ticket->assigned_to || $ticket->escalation_requested)
                                            <button onclick="openAssignModal({{ $ticket->id }})" 
                                                    class="text-green-600 hover:text-green-900 p-1 hover:bg-green-50 rounded"
                                                    title="{{ $ticket->escalation_requested ? 'Escalate' : 'Assign' }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <!-- No delete button for managers -->
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">No tickets found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assignment Modal with Request Escalation Option -->
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
                                <option value="">Select Agent/Manager...</option>
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

                    <div class="flex justify-between items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="openEscalationModalFromAssign()" class="px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-800 rounded-md font-medium flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            Request Escalation
                        </button>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeAssignModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium">Assign & Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('manager.tickets.detail-panel')

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
                
                // Set assigned agent
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

        function openEscalationModalFromAssign() {
            const ticketId = document.getElementById('assignTicketId').value;
            const replyTicketId = document.getElementById('replyTicketId');
            if (replyTicketId) {
                replyTicketId.value = ticketId;
            }
            closeAssignModal();
            openEscalationModal();
        }

        document.getElementById('assignForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const ticketId = document.getElementById('assignTicketId').value;
            const assignTo = document.getElementById('assignTo').value;
            const priority = document.getElementById('assignPriority').value;
            const category = document.getElementById('assignCategory').value;
            const statusId = document.getElementById('assignStatus').value;

            // First assign the ticket
            fetch(`/manager/tickets/${ticketId}/assign`, {
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
                if (!data.success) {
                    // If cannot assign to admin, show escalation option
                    if (data.message && data.message.includes('admin')) {
                        alert(data.message + '\n\nPlease use the "Request Escalation" button instead.');
                        return Promise.reject('Cannot assign to admin');
                    }
                    throw new Error(data.message || 'Error assigning ticket');
                }
                
                // Then update status if changed
                return fetch(`/manager/tickets/${ticketId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status_id: statusId })
                });
            })
            .then(response => {
                if (!response) return null; // Assignment failed
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    closeAssignModal();
                    window.location.reload();
                }
            })
            .catch(error => {
                if (error !== 'Cannot assign to admin') {
                    console.error('Error:', error);
                    alert('Error assigning ticket. Please try again.');
                }
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
    </script>

    <!-- Ticket Detail Slide-over Panel (Integrated) -->
    <div id="ticketDetailOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeTicketDetail()"></div>

    <div id="ticketDetailPanel" class="fixed inset-y-0 right-0 bg-white shadow-xl z-50 transform translate-x-full transition-all duration-300 ease-in-out" style="width: 50%; min-width: 400px; max-width: 100%;">
        <div id="resizeHandle" class="absolute left-0 top-0 bottom-0 w-1 bg-gray-300 hover:bg-indigo-500 cursor-ew-resize transition-colors"></div>
        
        <div class="h-full flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <div>
                    <h2 id="detailTicketId" class="text-lg font-semibold text-indigo-600"></h2>
                    <p id="detailTicketStatus" class="text-sm"></p>
                </div>
                <button onclick="closeTicketDetail()" class="text-gray-400 hover:text-gray-600 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="flex h-full">
                    <div class="w-64 bg-gray-50 border-r border-gray-200 p-4 flex-shrink-0">
                        <div class="space-y-4">
                            <div><label class="text-xs font-medium text-gray-500 uppercase">Ticket ID</label><p id="sidebarTicketId" class="text-sm font-semibold text-gray-900"></p></div>
                            <div><label class="text-xs font-medium text-gray-500 uppercase">Customer</label><p id="sidebarCustomer" class="text-sm text-gray-900"></p></div>
                            <div><label class="text-xs font-medium text-gray-500 uppercase">Assigned To</label><p id="sidebarAssignedTo" class="text-sm text-gray-900"></p></div>
                            <div><label class="text-xs font-medium text-gray-500 uppercase">Priority</label><p id="sidebarPriority" class="text-sm text-gray-900"></p></div>
                            <div><label class="text-xs font-medium text-gray-500 uppercase">Category</label><p id="sidebarCategory" class="text-sm text-gray-900"></p></div>
                            <div><label class="text-xs font-medium text-gray-500 uppercase">Created</label><p id="sidebarCreated" class="text-sm text-gray-900"></p></div>
                            <div><label class="text-xs font-medium text-gray-500 uppercase">Last Activity</label><p id="sidebarLastActivity" class="text-sm text-gray-900"></p></div>
                            <div><label class="text-xs font-medium text-gray-500 uppercase">Status</label><p id="sidebarStatus" class="mt-1"></p></div>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col">
                        <div class="p-6 border-b border-gray-200"><h3 id="detailTitle" class="text-xl font-semibold text-gray-900 mb-2"></h3></div>
                        <div id="conversationThread" class="flex-1 overflow-y-auto p-6 space-y-4"></div>
                        <div class="border-t border-gray-200 p-4 bg-gray-50" id="replySection"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="escalationModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeEscalationModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-6 pt-5 pb-4 bg-white">
                    <div class="flex items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-orange-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Request Escalation</h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-3">This ticket will be escalated for higher-level review. Please provide a reason.</p>
                                <div>
                                    <label for="escalationReason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Escalation <span class="text-red-500">*</span></label>
                                    <textarea id="escalationReason" rows="4" placeholder="Explain why this ticket needs higher-level attention..." class="w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm text-sm"></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Minimum 10 characters</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="submitEscalationRequest()" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 sm:ml-3 sm:w-auto sm:text-sm">Submit Request</button>
                    <button type="button" onclick="closeEscalationModal()" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentTicketStatusId = null;
        let currentTicketId = null;
        let isResizing = false;
        let lastDownX = 0;
        const panel = document.getElementById('ticketDetailPanel');
        const resizeHandle = document.getElementById('resizeHandle');

        resizeHandle.addEventListener('mousedown', function(e) { isResizing = true; lastDownX = e.clientX; document.body.style.cursor = 'ew-resize'; document.body.style.userSelect = 'none'; });
        document.addEventListener('mousemove', function(e) { if (!isResizing) return; const offsetRight = document.body.offsetWidth - e.clientX; const minWidth = 400; const maxWidth = window.innerWidth * 0.95; if (offsetRight >= minWidth && offsetRight <= maxWidth) { panel.style.width = offsetRight + 'px'; }});
        document.addEventListener('mouseup', function() { if (isResizing) { isResizing = false; document.body.style.cursor = ''; document.body.style.userSelect = ''; }});

        function openEscalationModal() { document.getElementById('escalationModal').classList.remove('hidden'); document.getElementById('escalationReason').value = ''; }
        function closeEscalationModal() { document.getElementById('escalationModal').classList.add('hidden'); }

        function submitEscalationRequest() {
            const reason = document.getElementById('escalationReason').value.trim();
            const ticketId = currentTicketId || document.getElementById('replyTicketId')?.value;
            if (!ticketId) { alert('Unable to identify ticket. Please refresh and try again.'); return; }
            if (!reason || reason.length < 10) { alert('Please provide a detailed reason for escalation (minimum 10 characters)'); return; }
            fetch(`/manager/tickets/${ticketId}/request-escalation`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ reason: reason })})
            .then(response => response.json())
            .then(data => { if (data.success) { closeEscalationModal(); alert(data.message); loadTicketDetails(ticketId); } else { alert(data.message || 'Error submitting escalation request'); }})
            .catch(error => { console.error('Error:', error); alert('Error submitting escalation request. Please try again.'); });
        }

        window.openTicketDetail = function(ticketId) { document.getElementById('ticketDetailOverlay').classList.remove('hidden'); document.getElementById('ticketDetailPanel').classList.remove('translate-x-full'); document.body.style.overflow = 'hidden'; loadTicketDetails(ticketId); }
        window.closeTicketDetail = function() { document.getElementById('ticketDetailOverlay').classList.add('hidden'); document.getElementById('ticketDetailPanel').classList.add('translate-x-full'); document.body.style.overflow = 'auto'; replySelectedFiles = []; updateReplyFileList(); }

        function loadTicketDetails(ticketId) {
            document.getElementById('conversationThread').innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
            fetch(`/tickets/${ticketId}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }})
            .then(response => response.json())
            .then(data => { populateTicketDetail(data); })
            .catch(error => { console.error('Error loading ticket:', error); document.getElementById('conversationThread').innerHTML = '<div class="text-center py-12 text-red-600"><p>Error loading ticket details. Please try again.</p></div>'; });
        }

        function populateTicketDetail(ticket) {
            const statusHtml = `<span class="px-2 py-1 text-xs font-semibold rounded-full" style="background-color: ${ticket.status_color}20; color: ${ticket.status_color}">${ticket.status}</span>`;
            const ticketIdFormatted = '#' + ticket.ticket_number;
            currentTicketStatusId = ticket.status_id; currentTicketId = ticket.id;
            document.getElementById('detailTicketId').textContent = ticketIdFormatted; document.getElementById('detailTicketStatus').innerHTML = statusHtml;
            document.getElementById('sidebarTicketId').textContent = ticketIdFormatted; document.getElementById('sidebarCustomer').textContent = ticket.customer_name || 'N/A';
            document.getElementById('sidebarAssignedTo').textContent = ticket.assigned_to_name || 'Unassigned'; document.getElementById('sidebarPriority').textContent = ticket.priority || 'Not Set';
            document.getElementById('sidebarCategory').textContent = ticket.category || 'Not Set'; document.getElementById('sidebarCreated').textContent = ticket.created_at_formatted;
            document.getElementById('sidebarLastActivity').textContent = ticket.updated_at_formatted; document.getElementById('sidebarStatus').innerHTML = statusHtml;
            document.getElementById('detailTitle').textContent = ticket.title;

            let conversationHtml = ''; const currentUserId = {{ auth()->id() }};
            conversationHtml += createMessageBubble({ author: ticket.customer_name || 'Customer', content: ticket.description, date: ticket.created_at_formatted, isCurrentUser: false, attachments: ticket.attachments || [], userRole: 'customer' });

            const timeline = [];
            if (ticket.escalations && ticket.escalations.length > 0) { ticket.escalations.forEach(escalation => { timeline.push({ type: 'escalation', timestamp: escalation.requested_at, data: escalation }); }); }
            if (ticket.reopenRequests && ticket.reopenRequests.length > 0) { ticket.reopenRequests.forEach(reopenReq => { timeline.push({ type: 'reopen_request', timestamp: reopenReq.requested_at, data: reopenReq }); }); }
            if (ticket.comments && ticket.comments.length > 0) { ticket.comments.forEach(comment => { timeline.push({ type: 'comment', timestamp: comment.created_at, data: comment }); }); }
            timeline.sort((a, b) => a.timestamp - b.timestamp);
            
            timeline.forEach(item => {
                if (item.type === 'escalation') { conversationHtml += createSystemMessage({ content: `🔺 <strong>Escalation Requested</strong><br>Reason: ${item.data.reason}`, date: item.data.requested_at_formatted || 'Recently', author: item.data.requested_by_name || 'Staff', isCurrentUser: item.data.requested_by_id === currentUserId }); }
                else if (item.type === 'reopen_request') { conversationHtml += createReopenRequestBubble(item.data, currentUserId, ticket.id); }
                else if (item.type === 'comment') { conversationHtml += createMessageBubble({ author: item.data.user_name, content: item.data.content, date: item.data.created_at_formatted, isCurrentUser: item.data.user_id === currentUserId, attachments: item.data.attachments || [], userRole: item.data.user_role }); }
            });

            document.getElementById('conversationThread').innerHTML = conversationHtml; populateReplySection(ticket);
        }

        function populateReplySection(ticket) {
            const replySection = document.getElementById('replySection'); const isClosed = ticket.status && ticket.status.toLowerCase() === 'closed';
            if (isClosed) {
                replySection.innerHTML = `<div class="bg-gray-100 border border-gray-300 rounded-md p-4 text-center"><p class="text-gray-700 font-medium mb-3"><svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>Ticket is closed. No further actions allowed.</p><div class="flex gap-2 justify-center"><button onclick="reopenTicket(${ticket.id})" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>Reopen Ticket</button><button type="button" onclick="openEscalationModal()" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>Request Escalation</button></div></div>`;
            } else {
                replySection.innerHTML = `<form id="replyForm" onsubmit="submitReply(event)"><input type="hidden" id="replyTicketId" value="${ticket.id}"><div class="mb-3"><textarea id="replyMessage" rows="3" placeholder="Type your reply here..." class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"></textarea></div><div class="mb-3"><div id="replyDropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-3 text-center cursor-pointer transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50"><p class="text-sm text-gray-600"><span class="font-semibold text-indigo-600">Click to attach files</span> or drag and drop</p><input id="replyAttachments" type="file" multiple class="hidden"></div><div id="replyFileList" class="mt-2 space-y-1"></div></div><div class="flex justify-between items-center gap-2"><button type="button" onclick="openEscalationModal()" class="text-sm text-orange-600 hover:text-orange-800 font-medium underline flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>Request Escalation</button><div class="relative inline-flex rounded-md shadow-sm"><button type="submit" class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-indigo-600 bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700">Send Reply</button><div class="relative"><button type="button" onclick="toggleStatusDropdown(event)" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-l-0 border-indigo-600 bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg></button><div id="statusDropdown" class="hidden absolute bottom-full right-0 mb-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"><div class="py-1"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase border-b border-gray-200">Change Status</div>@foreach($statuses as $status)<button type="button" onclick="changeStatus({{ $status->id }}, \'{{ $status->description }}\', \'{{ $status->color }}\')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><span class="inline-block w-3 h-3 rounded-full mr-2" style="background-color: {{ $status->color }}"></span>{{ $status->description }}</button>@endforeach</div></div></div></div></div></form>`;
                initializeReplyDropZone();
            }
        }

        function reopenTicket(ticketId) { if (!confirm('Are you sure you want to reopen this ticket?')) return; fetch(`/manager/tickets/${ticketId}/reopen`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }}).then(response => response.json()).then(data => { if (data.success) { loadTicketDetails(ticketId); updateTableRowStatus(ticketId, data.status_name, data.status_color); alert(data.message); } else { alert(data.message || 'Error reopening ticket'); }}).catch(error => { console.error('Error:', error); alert('Error reopening ticket. Please try again.'); }); }
        function toggleStatusDropdown(event) { event.preventDefault(); event.stopPropagation(); document.getElementById('statusDropdown').classList.toggle('hidden'); }
        document.addEventListener('click', function(event) { const dropdown = document.getElementById('statusDropdown'); if (dropdown && !dropdown.classList.contains('hidden')) { if (!event.target.closest('#statusDropdown') && !event.target.closest('button[onclick*="toggleStatusDropdown"]')) { dropdown.classList.add('hidden'); }}});

        function changeStatus(statusId, statusName, statusColor) {
            const ticketId = document.getElementById('replyTicketId').value; document.getElementById('statusDropdown').classList.add('hidden');
            fetch(`/manager/tickets/${ticketId}/status`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ status_id: statusId })})
            .then(response => response.json()).then(data => { if (data.success) { currentTicketStatusId = statusId; const statusHtml = `<span class="px-2 py-1 text-xs font-semibold rounded-full" style="background-color: ${statusColor}20; color: ${statusColor}">${statusName}</span>`; document.getElementById('detailTicketStatus').innerHTML = statusHtml; document.getElementById('sidebarStatus').innerHTML = statusHtml; document.getElementById('sidebarLastActivity').textContent = 'Just now'; updateTableRowStatus(ticketId, statusName, statusColor); } else { alert('Failed to update status'); }}).catch(error => { console.error('Error:', error); alert('Error updating status'); });
        }

        function updateTableRowStatus(ticketId, statusName, statusColor) { const rows = document.querySelectorAll('.ticket-row'); rows.forEach(row => { const ticketIdCell = row.querySelector('td:first-child'); if (!ticketIdCell) return; if (ticketIdCell.textContent.trim() === '#' + ticketId) { row.dataset.status = statusName.toLowerCase(); const statusCell = row.querySelector('td:nth-child(4)'); if (statusCell) { const statusBadge = statusCell.querySelector('span'); if (statusBadge) { statusBadge.style.backgroundColor = `${statusColor}20`; statusBadge.style.color = statusColor; statusBadge.textContent = statusName; }}}}); }

        function submitReply(event) {
            event.preventDefault(); const ticketId = document.getElementById('replyTicketId').value; const message = document.getElementById('replyMessage').value;
            if (!message.trim() && replySelectedFiles.length === 0) { alert('Please enter a message or attach at least one file'); return; }
            const submitButton = event.target.querySelector('button[type="submit"]'); const originalButtonText = submitButton.innerHTML; submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Sending...';
            const formData = new FormData(); formData.append('message', message); formData.append('_token', '{{ csrf_token() }}'); replySelectedFiles.forEach(file => { formData.append('attachments[]', file); });
            fetch(`/tickets/${ticketId}/reply`, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(response => response.json()).then(data => { if (data.success) { document.getElementById('replyMessage').value = ''; replySelectedFiles = []; document.getElementById('replyAttachments').value = ''; updateReplyFileList(); fetch(`/tickets/${ticketId}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }}).then(response => response.json()).then(ticket => { populateTicketDetail(ticket); setTimeout(() => { document.getElementById('conversationThread').scrollTop = document.getElementById('conversationThread').scrollHeight; }, 100); }); submitButton.disabled = false; submitButton.innerHTML = originalButtonText; } else { alert(data.message || 'Error sending reply'); submitButton.disabled = false; submitButton.innerHTML = originalButtonText; }}).catch(error => { console.error('Error:', error); alert('Error sending reply. Please try again.'); submitButton.disabled = false; submitButton.innerHTML = originalButtonText; });
        }

        let replySelectedFiles = [];
        function initializeReplyDropZone() { const replyDropZone = document.getElementById('replyDropZone'); const replyFileInput = document.getElementById('replyAttachments'); if (!replyDropZone || !replyFileInput) return; const newDropZone = replyDropZone.cloneNode(true); replyDropZone.parentNode.replaceChild(newDropZone, replyDropZone); const newFileInput = replyFileInput.cloneNode(true); replyFileInput.parentNode.replaceChild(newFileInput, replyFileInput); newDropZone.addEventListener('click', () => newFileInput.click()); newDropZone.addEventListener('dragover', (e) => { e.preventDefault(); newDropZone.classList.add('border-indigo-500', 'bg-indigo-50'); }); newDropZone.addEventListener('dragleave', (e) => { e.preventDefault(); newDropZone.classList.remove('border-indigo-500', 'bg-indigo-50'); }); newDropZone.addEventListener('drop', (e) => { e.preventDefault(); newDropZone.classList.remove('border-indigo-500', 'bg-indigo-50'); handleReplyFiles(e.dataTransfer.files); }); newFileInput.addEventListener('change', function(e) { handleReplyFiles(this.files); this.value = ''; }); }
        function handleReplyFiles(files) { const fileArray = Array.from(files); fileArray.forEach(file => { if (!replySelectedFiles.some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified)) { replySelectedFiles.push(file); }}); updateReplyFileList(); }
        function updateReplyFileList() { const container = document.getElementById('replyFileList'); container.innerHTML = ''; if (replySelectedFiles.length === 0) return; replySelectedFiles.forEach((file, index) => { const fileItem = document.createElement('div'); fileItem.className = 'flex items-center justify-between p-2 bg-white rounded-md border border-gray-200 text-sm'; fileItem.innerHTML = `<div class="flex items-center gap-2 flex-1 min-w-0"><svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg><span class="truncate">${file.name}</span><span class="text-xs text-gray-500 flex-shrink-0">(${formatFileSize(file.size)})</span></div><button type="button" onclick="removeReplyFile(${index})" class="text-red-500 hover:text-red-700 ml-2 flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>`; container.appendChild(fileItem); }); }
        function removeReplyFile(index) { replySelectedFiles.splice(index, 1); updateReplyFileList(); }
        function formatFileSize(bytes) { if (bytes === 0) return '0 Bytes'; const k = 1024; const sizes = ['Bytes', 'KB', 'MB', 'GB']; const i = Math.floor(Math.log(bytes) / Math.log(k)); return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]; }
        
        function createMessageBubble({ author, content, date, isCurrentUser, attachments, userRole }) { const alignClass = isCurrentUser ? 'justify-end' : 'justify-start'; const bubbleClass = isCurrentUser ? 'bg-indigo-600 text-white' : 'bg-white text-gray-900 border border-gray-200'; const authorClass = isCurrentUser ? 'text-right' : 'text-left'; const showMessage = content !== '[Attachment]'; let attachmentsHtml = ''; if (attachments && attachments.length > 0) { attachmentsHtml = `<div class="mt-3 space-y-2">${attachments.map(att => createAttachmentElement(att, isCurrentUser)).join('')}</div>`; } let rolePrefix = ''; if (userRole && userRole !== 'customer') { const roleColors = { 'admin': 'text-red-600', 'manager': 'text-blue-600', 'agent': 'text-green-600' }; const roleClass = roleColors[userRole] || 'text-gray-600'; const roleText = userRole.charAt(0).toUpperCase() + userRole.slice(1); rolePrefix = `<span class="text-xs ${roleClass}">[${roleText}]</span> `; } return `<div class="flex ${alignClass}"><div class="max-w-md"><p class="text-xs text-gray-500 mb-1 ${authorClass}">${rolePrefix}${author} • ${date}</p><div class="${bubbleClass} rounded-lg px-4 py-3 shadow-sm">${showMessage ? `<p class="text-sm whitespace-pre-wrap">${content}</p>` : ''}${attachmentsHtml}</div></div></div>`; }
        function createSystemMessage({ content, date, author, isCurrentUser }) { const alignClass = isCurrentUser ? 'justify-end' : 'justify-start'; const authorClass = isCurrentUser ? 'text-right' : 'text-left'; return `<div class="flex ${alignClass} my-4"><div class="max-w-lg"><p class="text-xs text-gray-500 mb-1 ${authorClass}">${author} • ${date}</p><div class="bg-orange-50 border-l-4 border-orange-500 rounded-r-lg px-4 py-3 shadow-sm"><div class="flex items-start"><svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg><div class="flex-1"><p class="text-sm text-orange-900">${content}</p></div></div></div></div></div>`; }
        
        function createReopenRequestBubble(reopenReq, currentUserId, ticketId) {
            const roleColors = { 'admin': 'text-red-600', 'manager': 'text-blue-600', 'agent': 'text-green-600', 'customer': 'text-purple-600' };
            const statusColor = reopenReq.status === 'pending' ? '#3b82f6' : (reopenReq.status === 'accepted' ? '#10b981' : '#ef4444');
            const statusBg = reopenReq.status === 'pending' ? '#dbeafe' : (reopenReq.status === 'accepted' ? '#d1fae5' : '#fee2e2');
            const statusText = reopenReq.status === 'pending' ? 'Pending Review' : (reopenReq.status === 'accepted' ? 'Approved' : 'Rejected');
            
            // Build requester role prefix
            const reqRole = reopenReq.requested_by_role || 'customer';
            const reqRoleClass = roleColors[reqRole] || 'text-gray-600';
            const reqRoleText = reqRole.charAt(0).toUpperCase() + reqRole.slice(1);
            const requesterPrefix = `<span class="${reqRoleClass}">[${reqRoleText}]</span> `;
            
            let actionButtons = '';
            let pendingInfo = '';
            if (reopenReq.status === 'pending') {
                actionButtons = `<div class="mt-3 flex gap-2"><button onclick="showRemarksModalForIndex(${ticketId}, ${reopenReq.id}, 'accept')" class="px-3 py-2 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Approve & Reopen</button><button onclick="showRemarksModalForIndex(${ticketId}, ${reopenReq.id}, 'decline')" class="px-3 py-2 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Reject</button></div>`;
                // Add pending info section
                pendingInfo = `<div class="mt-2 p-2 bg-blue-100 bg-opacity-50 rounded text-xs">
                    <p class="text-blue-800"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Awaiting review by Admin or Manager</p>
                    <p class="text-blue-700 mt-1">Submitted: ${reopenReq.requested_at_formatted || 'Recently'}</p>
                </div>`;
            }
            
            let responseHtml = '';
            if (reopenReq.status !== 'pending' && reopenReq.responded_by_name) {
                const respRole = reopenReq.responded_by_role || 'staff';
                const respRoleClass = roleColors[respRole] || 'text-gray-600';
                const respRoleText = respRole.charAt(0).toUpperCase() + respRole.slice(1);
                const respRolePrefix = `<span class="${respRoleClass}">[${respRoleText}]</span> `;
                
                const responseIcon = reopenReq.status === 'accepted' 
                    ? '<svg class="w-4 h-4 inline mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                    : '<svg class="w-4 h-4 inline mr-1 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
                
                responseHtml = `<div class="mt-3 pt-3 border-t" style="border-color: ${statusColor}40;">
                    <p class="text-xs font-medium mb-1">${responseIcon}<strong>${reopenReq.status === 'accepted' ? 'Approved' : 'Rejected'} by ${respRolePrefix}${reopenReq.responded_by_name}</strong></p>
                    <p class="text-xs opacity-80">on ${reopenReq.responded_at_formatted || 'recently'}</p>
                    ${reopenReq.remarks ? `<div class="mt-2 p-2 bg-white bg-opacity-50 rounded"><p class="text-xs font-semibold">Remarks:</p><p class="text-xs opacity-90">${reopenReq.remarks}</p></div>` : ''}
                </div>`;
            }
            
            return `<div class="flex justify-start my-4"><div class="max-w-2xl w-full"><p class="text-xs text-gray-500 mb-1">${requesterPrefix}${reopenReq.requested_by_name || 'User'} • ${reopenReq.requested_at_formatted || 'Recently'}</p><div class="rounded-lg p-4 border-l-4 shadow-sm" style="background-color: ${statusBg}; border-color: ${statusColor};"><div class="flex items-start gap-3"><svg class="w-6 h-6 flex-shrink-0 mt-0.5" style="color: ${statusColor};" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><div class="flex-1"><div class="flex items-start justify-between mb-2"><h4 class="font-semibold text-sm" style="color: ${statusColor};"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>Reopen Request</h4><span class="px-2 py-1 text-xs font-semibold rounded-full ml-2" style="background-color: ${statusColor}20; color: ${statusColor};">${statusText}</span></div><p class="text-sm mb-2" style="color: ${statusColor === '#3b82f6' ? '#1e3a8a' : '#374151'};"><strong>Reason:</strong> ${reopenReq.reason || 'No reason provided'}</p>${pendingInfo}${responseHtml}${actionButtons}</div></div></div></div></div>`;
        }
        
        // Remarks modal for index page reopen request handling
        let currentReopenTicketId = null;
        let currentReopenRequestId = null;
        let currentReopenAction = null;
        
        function showRemarksModalForIndex(ticketId, reopenRequestId, action) {
            currentReopenTicketId = ticketId;
            currentReopenRequestId = reopenRequestId;
            currentReopenAction = action;
            
            const modal = document.getElementById('remarksModalIndex');
            const title = document.getElementById('remarksModalIndexTitle');
            const textarea = document.getElementById('remarksTextareaIndex');
            
            if (action === 'accept') {
                title.textContent = 'Approve Reopen Request';
                title.className = 'text-lg font-semibold text-green-700';
            } else {
                title.textContent = 'Reject Reopen Request';
                title.className = 'text-lg font-semibold text-red-700';
            }
            
            textarea.value = '';
            modal.classList.remove('hidden');
        }
        
        function closeRemarksModalIndex() {
            document.getElementById('remarksModalIndex').classList.add('hidden');
            currentReopenTicketId = null;
            currentReopenRequestId = null;
            currentReopenAction = null;
        }
        
        function submitRemarksIndex() {
            const remarks = document.getElementById('remarksTextareaIndex').value.trim();
            
            fetch(`/tickets/${currentReopenTicketId}/reopen-requests/${currentReopenRequestId}/respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ action: currentReopenAction, remarks: remarks })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeRemarksModalIndex();
                    alert(data.message);
                    loadTicketDetails(currentReopenTicketId);
                    if (data.action === 'accept') {
                        const openStatus = window.availableStatuses.find(s => s.description === 'Open');
                        if (openStatus) {
                            updateTableRowStatus(currentReopenTicketId, 'Open', openStatus.color);
                        }
                    }
                } else {
                    alert(data.message || 'Error responding to reopen request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error responding to reopen request. Please try again.');
            });
        }
        
        function respondToReopenRequest(ticketId, reopenRequestId, action) { if (!confirm(`Are you sure you want to ${action} this reopen request?`)) return; fetch(`/tickets/${ticketId}/reopen-requests/${reopenRequestId}/respond`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ action: action })}).then(response => response.json()).then(data => { if (data.success) { alert(data.message); loadTicketDetails(ticketId); if (data.action === 'accept') { const openStatus = window.availableStatuses.find(s => s.description === 'Open'); if (openStatus) { updateTableRowStatus(ticketId, 'Open', openStatus.color); }}} else { alert(data.message || 'Error responding to reopen request'); }}).catch(error => { console.error('Error:', error); alert('Error responding to reopen request. Please try again.'); }); }
        
        function createAttachmentElement(attachment, isCurrentUser) { const fileExtension = attachment.file_name.split('.').pop().toLowerCase(); const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension); const isPdf = fileExtension === 'pdf'; const isDoc = ['doc', 'docx'].includes(fileExtension); const textColorClass = isCurrentUser ? 'text-indigo-200 hover:text-white' : 'text-indigo-600 hover:text-indigo-800'; const bgClass = isCurrentUser ? 'bg-white bg-opacity-20' : 'bg-gray-50'; const borderClass = isCurrentUser ? 'border-white border-opacity-30' : 'border-gray-200'; if (isImage) { return `<div class="attachment-item"><div class="block ${textColorClass} cursor-pointer" onclick="openImageModal('${attachment.file_path}', '${attachment.file_name}'); event.preventDefault();"><div class="${bgClass} rounded-md p-2 border ${borderClass}"><img src="/tickets/attachment/${attachment.file_path}" alt="${attachment.file_name}" class="max-w-full h-auto max-h-32 rounded object-cover hover:opacity-80 transition-opacity" style="max-width: 200px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"><div style="display: none;" class="flex items-center gap-2 text-xs"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg><span>${attachment.file_name}</span></div><p class="text-xs mt-1 text-center opacity-80">${attachment.file_name}</p></div></div></div>`; } else if (isPdf) { return `<div class="attachment-item"><a href="/tickets/attachment/${attachment.file_path}" target="_blank" class="flex items-center gap-2 text-xs ${textColorClass} ${bgClass} rounded-md p-2 border ${borderClass} hover:opacity-80 transition-all"><svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6l-4-4H4v16zm6-10V4l4 4h-4z"/></svg><div class="flex-1 min-w-0"><p class="truncate font-medium">${attachment.file_name}</p><p class="text-xs opacity-75">${formatFileSize(attachment.file_size)}</p></div><svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg></a></div>`; } else if (isDoc) { return `<div class="attachment-item"><a href="/tickets/attachment/${attachment.file_path}" target="_blank" class="flex items-center gap-2 text-xs ${textColorClass} ${bgClass} rounded-md p-2 border ${borderClass} hover:opacity-80 transition-all"><svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6l-4-4H4v16zm6-10V4l4 4h-4z"/></svg><div class="flex-1 min-w-0"><p class="truncate font-medium">${attachment.file_name}</p><p class="text-xs opacity-75">${formatFileSize(attachment.file_size)}</p></div><svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg></a></div>`; } else { return `<div class="attachment-item"><a href="/tickets/attachment/${attachment.file_path}" target="_blank" class="flex items-center gap-2 text-xs ${textColorClass} ${bgClass} rounded-md p-2 border ${borderClass} hover:opacity-80 transition-all"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg><div class="flex-1 min-w-0"><p class="truncate font-medium">${attachment.file_name}</p><p class="text-xs opacity-75">${formatFileSize(attachment.file_size)}</p></div><svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg></a></div>`; }}
        
        function openImageModal(imagePath, fileName) { event.preventDefault(); const modal = document.createElement('div'); modal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4'; modal.innerHTML = `<div class="max-w-6xl max-h-full w-full flex flex-col"><div class="bg-white rounded-t-lg px-6 py-4 flex items-center justify-between"><div class="flex items-center gap-3"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg><h3 class="text-lg font-medium text-gray-900 truncate">${fileName}</h3></div><div class="flex items-center gap-2"><a href="/tickets/attachment/${imagePath}" download="${fileName}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="Download"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg></a><button onclick="this.closest('.fixed').remove()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div><div class="bg-black rounded-b-lg overflow-auto flex items-center justify-center" style="max-height: calc(90vh - 70px);"><img src="/tickets/attachment/${imagePath}" alt="${fileName}" class="max-w-full h-auto object-contain" style="max-height: calc(90vh - 100px);"></div></div>`; modal.addEventListener('click', function(e) { if (e.target === modal) { modal.remove(); }}); const closeHandler = function(e) { if (e.key === 'Escape') { modal.remove(); document.removeEventListener('keydown', closeHandler); }}; document.addEventListener('keydown', closeHandler); document.body.appendChild(modal); }

        // Auto-open ticket detail panel if ticket_id is in URL
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ticketId = urlParams.get('ticket_id');
            
            if (ticketId) {
                console.log('Auto-opening ticket:', ticketId);
                // Longer delay to ensure all scripts are loaded
                setTimeout(() => {
                    if (typeof openTicketDetail === 'function') {
                        openTicketDetail(ticketId);
                    } else {
                        console.error('openTicketDetail function not found');
                    }
                }, 500);
            }
        });
    </script>

    <!-- Remarks Modal for Reopen Request (Index Page) -->
    <div id="remarksModalIndex" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="remarksModalIndexTitle" class="text-lg font-semibold text-gray-900">Respond to Reopen Request</h3>
                    <button onclick="closeRemarksModalIndex()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <label for="remarksTextareaIndex" class="block text-sm font-medium text-gray-700 mb-2">
                        Remarks (optional)
                    </label>
                    <textarea id="remarksTextareaIndex" rows="4"
                        placeholder="Add any remarks or notes for this decision..."
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRemarksModalIndex()" 
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md text-sm">
                        Cancel
                    </button>
                    <button type="button" onclick="submitRemarksIndex()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
