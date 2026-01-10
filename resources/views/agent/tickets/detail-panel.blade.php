<!-- Ticket Detail Slide-over Panel -->
<div id="ticketDetailOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeTicketDetail()"></div>

<div id="ticketDetailPanel" class="fixed inset-y-0 right-0 bg-white shadow-xl z-50 transform translate-x-full transition-all duration-300 ease-in-out" style="width: 75%; min-width: 25vw; max-width: 100%;">
    <!-- Resize Handle -->
    <div id="resizeHandle" class="absolute left-0 top-0 bottom-0 w-1 bg-gray-300 hover:bg-indigo-500 cursor-ew-resize transition-colors"></div>
    
    <div class="h-full flex flex-col">
        <!-- Panel Header -->
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

        <!-- Panel Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="flex h-full">
                <!-- Left Sidebar - Ticket Info -->
                <div class="w-64 bg-gray-50 border-r border-gray-200 p-4 flex-shrink-0">
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Ticket ID</label>
                            <p id="sidebarTicketId" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Customer</label>
                            <p id="sidebarCustomer" class="text-sm text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Assigned To</label>
                            <p id="sidebarAssignedTo" class="text-sm text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Priority</label>
                            <p id="sidebarPriority" class="text-sm text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Category</label>
                            <p id="sidebarCategory" class="text-sm text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Created</label>
                            <p id="sidebarCreated" class="text-sm text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Last Activity</label>
                            <p id="sidebarLastActivity" class="text-sm text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Status</label>
                            <p id="sidebarStatus" class="mt-1"></p>
                        </div>
                    </div>
                </div>

                <!-- Main Content - Conversation -->
                <div class="flex-1 flex flex-col">
                    <!-- Ticket Title & Description -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 id="detailTitle" class="text-xl font-semibold text-gray-900 mb-2"></h3>
                    </div>

                    <!-- Conversation Thread -->
                    <div id="conversationThread" class="flex-1 overflow-y-auto p-6 space-y-4">
                        <!-- Messages will be loaded here -->
                    </div>

                    <!-- Reply Section -->
                    <div class="border-t border-gray-200 p-4 bg-gray-50" id="replySection">
                        <!-- This will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Escalation Request Modal -->
<div id="escalationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeEscalationModal()"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 pt-5 pb-4 bg-white">
                <div class="flex items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-orange-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            Request Escalation
                        </h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-3">
                                This ticket will be escalated for higher-level review and assignment. Please provide a reason for the escalation request.
                            </p>
                            <div>
                                <label for="escalationReason" class="block text-sm font-medium text-gray-700 mb-1">
                                    Reason for Escalation <span class="text-red-500">*</span>
                                </label>
                                <textarea id="escalationReason" 
                                          rows="4" 
                                          placeholder="Explain why this ticket needs higher-level attention..."
                                          class="w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm text-sm"></textarea>
                                <p class="mt-1 text-xs text-gray-500">Minimum 10 characters</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        onclick="submitEscalationRequest()"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Submit Request
                </button>
                <button type="button" 
                        onclick="closeEscalationModal()"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Remarks Modal for Reopen Request -->
<div id="remarksModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="remarks-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeRemarksModal()"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 pt-5 pb-4 bg-white">
                <div class="flex items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="remarksModalTitle">
                            Approve/Reject Reopen Request
                        </h3>
                        <div class="mt-4">
                            <div>
                                <label for="remarksTextarea" class="block text-sm font-medium text-gray-700 mb-1">
                                    Remarks/Comments (Optional)
                                </label>
                                <textarea id="remarksTextarea" 
                                          rows="4" 
                                          placeholder="Add any remarks or comments about your decision..."
                                          class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"></textarea>
                                <p class="mt-1 text-xs text-gray-500">This will be visible to the requester and other staff members.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        id="remarksModalAction"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Submit
                </button>
                <button type="button" 
                        onclick="closeRemarksModal()"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Re-Escalation Modal -->
<div id="reEscalationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="re-escalation-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeReEscalationModal()"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 pt-5 pb-4 bg-white">
                <div class="flex items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-orange-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="reEscalationModalTitle">
                            Request Re-Escalation
                        </h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-3">
                                The issue has not been resolved. Please describe what's still wrong:
                            </p>
                            <div>
                                <label for="reEscalationReason" class="block text-sm font-medium text-gray-700 mb-1">
                                    Reason for Re-Escalation <span class="text-red-500">*</span>
                                </label>
                                <textarea id="reEscalationReason" 
                                          rows="4" 
                                          placeholder="Describe why the issue is not resolved. What error still occurs? What behavior is still incorrect?"
                                          class="w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm text-sm"
                                          required></textarea>
                                <p class="mt-1 text-xs text-gray-500">Minimum 10 characters.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        id="reEscalationModalSubmit"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Request Re-Escalation
                </button>
                <button type="button" 
                        onclick="closeReEscalationModal()"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Agent Reopen Request Modal -->
<div id="agentReopenModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="agent-reopen-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeAgentReopenModal()"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 pt-5 pb-4 bg-white">
                <div class="flex items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-blue-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="agentReopenModalTitle">
                            Request to Reopen Ticket
                        </h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-3">
                                This ticket is closed. Please explain why it needs to be reopened:
                            </p>
                            <div>
                                <label for="agentReopenReason" class="block text-sm font-medium text-gray-700 mb-1">
                                    Reason for Reopening <span class="text-red-500">*</span>
                                </label>
                                <textarea id="agentReopenReason" 
                                          rows="4" 
                                          placeholder="Explain why this ticket needs to be reopened..."
                                          class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm"
                                          required></textarea>
                                <p class="mt-1 text-xs text-gray-500">Minimum 10 characters. Your request will be reviewed by a manager or admin.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        id="agentReopenModalSubmit"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Submit Request
                </button>
                <button type="button" 
                        onclick="closeAgentReopenModal()"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentTicketStatusId = null;
    let currentTicketId = null;

    // Resizable panel functionality
    let isResizing = false;
    let lastDownX = 0;

    const panel = document.getElementById('ticketDetailPanel');
    const resizeHandle = document.getElementById('resizeHandle');

    resizeHandle.addEventListener('mousedown', function(e) {
        isResizing = true;
        lastDownX = e.clientX;
        document.body.style.cursor = 'ew-resize';
        document.body.style.userSelect = 'none';
    });

    document.addEventListener('mousemove', function(e) {
        if (!isResizing) return;

        const offsetRight = document.body.offsetWidth - e.clientX;
        const minWidth = Math.floor(window.innerWidth * 0.25);
        const maxWidth = window.innerWidth * 0.95;

        if (offsetRight >= minWidth && offsetRight <= maxWidth) {
            panel.style.width = offsetRight + 'px';
        }
    });

    document.addEventListener('mouseup', function() {
        if (isResizing) {
            isResizing = false;
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
        }
    });

    // Escalation Modal Functions
    function openEscalationModal() {
        document.getElementById('escalationModal').classList.remove('hidden');
        document.getElementById('escalationReason').value = '';
    }

    function closeEscalationModal() {
        document.getElementById('escalationModal').classList.add('hidden');
    }

    function submitEscalationRequest() {
        const reason = document.getElementById('escalationReason').value.trim();
        const ticketId = currentTicketId || document.getElementById('replyTicketId')?.value;
        
        if (!ticketId) {
            alert('Unable to identify ticket. Please refresh and try again.');
            return;
        }
        
        if (!reason || reason.length < 10) {
            alert('Please provide a detailed reason for escalation (minimum 10 characters)');
            return;
        }

        fetch(`/agent/tickets/${ticketId}/request-escalation`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEscalationModal();
                alert(data.message);
                loadTicketDetails(ticketId);
            } else {
                alert(data.message || 'Error submitting escalation request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting escalation request. Please try again.');
        });
    }

    // Ticket Detail Panel Functions
    function openTicketDetail(ticketId) {
        document.getElementById('ticketDetailOverlay').classList.remove('hidden');
        document.getElementById('ticketDetailPanel').classList.remove('translate-x-full');
        document.body.style.overflow = 'hidden';
        
        loadTicketDetails(ticketId);
    }

    function closeTicketDetail() {
        document.getElementById('ticketDetailOverlay').classList.add('hidden');
        document.getElementById('ticketDetailPanel').classList.add('translate-x-full');
        document.body.style.overflow = 'auto';
        replySelectedFiles = [];
        updateReplyFileList();
    }

    function loadTicketDetails(ticketId) {
        document.getElementById('conversationThread').innerHTML = `
            <div class="flex items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        `;

        fetch(`/tickets/${ticketId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            populateTicketDetail(data);
        })
        .catch(error => {
            console.error('Error loading ticket:', error);
            document.getElementById('conversationThread').innerHTML = `
                <div class="text-center py-12 text-red-600">
                    <p>Error loading ticket details. Please try again.</p>
                </div>
            `;
        });
    }

    function populateTicketDetail(ticket) {
        const statusHtml = `
            <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                  style="background-color: ${ticket.status_color}20; color: ${ticket.status_color}">
                ${ticket.status}
            </span>
        `;

        const ticketIdFormatted = '#' + ticket.ticket_number;
        
        currentTicketStatusId = ticket.status_id;
        currentTicketId = ticket.id;
        
        // Header
        document.getElementById('detailTicketId').textContent = ticketIdFormatted;
        document.getElementById('detailTicketStatus').innerHTML = statusHtml;

        // Sidebar
        document.getElementById('sidebarTicketId').textContent = ticketIdFormatted;
        document.getElementById('sidebarCustomer').textContent = ticket.customer_name || 'N/A';
        document.getElementById('sidebarAssignedTo').textContent = ticket.assigned_to_name || 'Unassigned';
        document.getElementById('sidebarPriority').textContent = ticket.priority || 'Not Set';
        document.getElementById('sidebarCategory').textContent = ticket.category || 'Not Set';
        document.getElementById('sidebarCreated').textContent = ticket.created_at_formatted;
        document.getElementById('sidebarLastActivity').textContent = ticket.updated_at_formatted;
        document.getElementById('sidebarStatus').innerHTML = statusHtml;

        // Title
        document.getElementById('detailTitle').textContent = ticket.title;

        // Conversation Thread
        let conversationHtml = '';
        
        const currentUserId = {{ auth()->id() }};
        
        conversationHtml += createMessageBubble({
            author: ticket.customer_name || 'Customer',
            content: ticket.description,
            date: ticket.created_at_formatted,
            isCurrentUser: false,
            attachments: ticket.attachments || [],
            userRole: 'customer'
        });

        // Merge escalations, reopen requests, and comments chronologically
        const timeline = [];
        
        // Add all escalations to timeline (exclude manager-requested escalations)
        if (ticket.escalations && ticket.escalations.length > 0) {
            ticket.escalations.forEach(escalation => {
                // Skip escalations requested by managers - agents should not see these
                if (escalation.requested_by_role === 'manager') {
                    return;
                }
                
                timeline.push({
                    type: 'escalation',
                    timestamp: escalation.requested_at,
                    data: escalation
                });
            });
        }
        
        // Add all reopen requests to timeline
        if (ticket.reopenRequests && ticket.reopenRequests.length > 0) {
            ticket.reopenRequests.forEach(reopenReq => {
                timeline.push({
                    type: 'reopen_request',
                    timestamp: reopenReq.requested_at,
                    data: reopenReq
                });
            });
        }
        
        // Add all comments to timeline
        if (ticket.comments && ticket.comments.length > 0) {
            ticket.comments.forEach(comment => {
                timeline.push({
                    type: 'comment',
                    timestamp: comment.created_at,
                    data: comment
                });
            });
        }
        
        // Sort timeline by timestamp
        timeline.sort((a, b) => a.timestamp - b.timestamp);
        
        // Render timeline items
        timeline.forEach(item => {
            if (item.type === 'escalation') {
                const escalation = item.data;
                conversationHtml += createEscalationBubble(escalation, currentUserId, ticket.id, ticket.status, ticket.escalations);
            } else if (item.type === 'reopen_request') {
                const reopenReq = item.data;
                conversationHtml += createReopenRequestBubble(reopenReq, currentUserId, ticket.id);
            } else if (item.type === 'comment') {
                const comment = item.data;
                const isCurrentUserComment = comment.user_id === currentUserId;
                conversationHtml += createMessageBubble({
                    author: comment.user_name || 'User',
                    content: comment.content,
                    date: comment.created_at_formatted,
                    isCurrentUser: isCurrentUserComment,
                    attachments: comment.attachments || [],
                    userRole: comment.user_role
                });
            }
        });

        document.getElementById('conversationThread').innerHTML = conversationHtml;
        
        populateReplySection(ticket);
    }

    function populateReplySection(ticket) {
        const replySection = document.getElementById('replySection');
        const isClosed = ticket.status && ticket.status.toLowerCase() === 'closed';
        
        if (isClosed) {
            // Check if there's already a pending reopen request from this agent
            const hasPendingReopenRequest = ticket.reopenRequests && ticket.reopenRequests.some(
                req => req.requested_by_id === {{ auth()->id() }} && req.status === 'pending'
            );
            
            // Show closed ticket notice with reopen request option for agents
            replySection.innerHTML = `
                <div class="bg-gray-100 border border-gray-300 rounded-md p-4 text-center">
                    <p class="text-gray-700 font-medium mb-3">
                        <svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Ticket is closed. No further actions allowed.
                    </p>
                    ${hasPendingReopenRequest ? `
                        <span class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 bg-gray-200 rounded-md cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Reopen Request Already Pending
                        </span>
                    ` : `
                        <button type="button" 
                                onclick="showAgentReopenModal(${ticket.id})"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-2 mx-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Request to Reopen Ticket
                        </button>
                    `}
                </div>
            `;
        } else {
            // Show normal reply form
            replySection.innerHTML = `
                <form id="replyForm" onsubmit="submitReply(event)">
                    <input type="hidden" id="replyTicketId" name="ticket_id" value="${ticket.id}">
                    
                    <div class="mb-3">
                        <textarea id="replyMessage" name="message" rows="3" 
                            placeholder="Type your reply here..."
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"></textarea>
                    </div>

                    <!-- Reply Attachments -->
                    <div class="mb-3">
                        <div id="replyDropZone" 
                             class="border-2 border-dashed border-gray-300 rounded-lg p-3 text-center cursor-pointer transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50">
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold text-indigo-600">Click to attach files</span> or drag and drop
                            </p>
                            <input id="replyAttachments" type="file" name="attachments[]" multiple class="hidden">
                        </div>
                        <div id="replyFileList" class="mt-2 space-y-1"></div>
                    </div>

                    <div class="flex justify-between items-center gap-2">
                        <!-- Request Escalation Link -->
                        ${ticket.escalations && ticket.escalations.some(esc => !esc.escalated_by_id) ? `
                            <span class="text-sm text-gray-500 font-medium flex items-center gap-1 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Requested Escalation
                            </span>
                        ` : `
                            <button type="button" 
                                    onclick="openEscalationModal()"
                                    class="text-sm text-orange-600 hover:text-orange-800 font-medium underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Request Escalation
                            </button>
                        `}

                        <!-- Combined Button: Send Reply + Status Dropdown -->
                        <div class="relative inline-flex rounded-md shadow-sm">
                            <button type="submit" 
                                    class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-indigo-600 bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Send Reply
                            </button>
                            <div class="relative">
                                <button type="button" 
                                        onclick="toggleStatusDropdown(event)"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-l-0 border-indigo-600 bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    </svg>
                                </button>
                                <!-- Status Dropdown (appears upward) - EXCLUDES ESCALATED -->
                                <div id="statusDropdown" class="hidden absolute bottom-full right-0 mb-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                    <div class="py-1" role="menu">
                                        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase border-b border-gray-200">
                                            Change Status
                                        </div>
                                        @foreach($statuses as $status)
                                            @if($status->description !== 'Escalated')
                                                <button type="button" 
                                                        onclick="changeStatus({{ $status->id }}, '{{ $status->description }}', '{{ $status->color }}')"
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                        role="menuitem">
                                                    <span class="inline-block w-3 h-3 rounded-full mr-2" style="background-color: {{ $status->color }}"></span>
                                                    {{ $status->description }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            `;
            
            initializeReplyDropZone();
        }
    }

    // Status Dropdown Functions
    function toggleStatusDropdown(event) {
        event.preventDefault();
        event.stopPropagation();
        const dropdown = document.getElementById('statusDropdown');
        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('statusDropdown');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            if (!event.target.closest('#statusDropdown') && !event.target.closest('button[onclick*="toggleStatusDropdown"]')) {
                dropdown.classList.add('hidden');
            }
        }
    });

    function changeStatus(statusId, statusName, statusColor) {
        const ticketId = document.getElementById('replyTicketId').value;
        
        document.getElementById('statusDropdown').classList.add('hidden');
        
        fetch(`/agent/tickets/${ticketId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status_id: statusId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentTicketStatusId = statusId;
                const statusHtml = `
                    <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                          style="background-color: ${statusColor}20; color: ${statusColor}">
                        ${statusName}
                    </span>
                `;
                document.getElementById('detailTicketStatus').innerHTML = statusHtml;
                document.getElementById('sidebarStatus').innerHTML = statusHtml;
                document.getElementById('sidebarLastActivity').textContent = 'Just now';
                
                updateTableRowStatus(ticketId, statusName, statusColor);
                
                // Reload ticket details to update reply section if status is Closed
                loadTicketDetails(ticketId);
            } else {
                alert('Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error updating status:', error);
            alert('Error updating status');
        });
    }

    function updateTableRowStatus(ticketId, statusName, statusColor) {
        const rows = document.querySelectorAll('.ticket-row');
        rows.forEach(row => {
            const ticketIdCell = row.querySelector('td:first-child');
            if (!ticketIdCell) return;
            
            const rowTicketNumber = ticketIdCell.textContent.trim();
            const currentTicketNumber = '#' + ticketId;
            
            if (rowTicketNumber === currentTicketNumber) {
                row.dataset.status = statusName.toLowerCase();
                
                const statusCell = row.querySelector('td:nth-child(4)');
                if (statusCell) {
                    const statusBadge = statusCell.querySelector('span');
                    if (statusBadge) {
                        statusBadge.style.backgroundColor = `${statusColor}20`;
                        statusBadge.style.color = statusColor;
                        statusBadge.textContent = statusName;
                    }
                }
            }
        });
    }

    function submitReply(event) {
        event.preventDefault();
        
        const ticketId = document.getElementById('replyTicketId').value;
        const message = document.getElementById('replyMessage').value;
        
        if (!message.trim() && replySelectedFiles.length === 0) {
            alert('Please enter a message or attach at least one file');
            return;
        }

        const submitButton = event.target.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Sending...';

        const formData = new FormData();
        formData.append('message', message);
        formData.append('_token', '{{ csrf_token() }}');
        
        replySelectedFiles.forEach(file => {
            formData.append('attachments[]', file);
        });

        const conversationThread = document.getElementById('conversationThread');

        fetch(`/tickets/${ticketId}/reply`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('replyMessage').value = '';
                replySelectedFiles = [];
                document.getElementById('replyAttachments').value = '';
                updateReplyFileList();
                
                fetch(`/tickets/${ticketId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(ticket => {
                    populateTicketDetail(ticket);
                    
                    setTimeout(() => {
                        conversationThread.scrollTop = conversationThread.scrollHeight;
                    }, 100);
                });
                
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            } else {
                alert(data.message || 'Error sending reply');
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending reply. Please try again.');
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        });
    }

    let replySelectedFiles = [];

    function initializeReplyDropZone() {
        const replyDropZone = document.getElementById('replyDropZone');
        const replyFileInput = document.getElementById('replyAttachments');
        
        if (!replyDropZone || !replyFileInput) return;

        const newDropZone = replyDropZone.cloneNode(true);
        replyDropZone.parentNode.replaceChild(newDropZone, replyDropZone);
        
        const newFileInput = replyFileInput.cloneNode(true);
        replyFileInput.parentNode.replaceChild(newFileInput, replyFileInput);

        newDropZone.addEventListener('click', () => newFileInput.click());

        newDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            newDropZone.classList.add('border-indigo-500', 'bg-indigo-50');
        });

        newDropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            newDropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        });

        newDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            newDropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
            handleReplyFiles(e.dataTransfer.files);
        });

        newFileInput.addEventListener('change', function(e) {
            handleReplyFiles(this.files);
            this.value = '';
        });
    }

    function handleReplyFiles(files) {
        const fileArray = Array.from(files);
        fileArray.forEach(file => {
            if (!replySelectedFiles.some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified)) {
                replySelectedFiles.push(file);
            }
        });
        updateReplyFileList();
    }

    function updateReplyFileList() {
        const container = document.getElementById('replyFileList');
        if (!container) return; // Check if element exists
        
        container.innerHTML = '';
        
        if (replySelectedFiles.length === 0) return;
        
        replySelectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-white rounded-md border border-gray-200 text-sm';
            fileItem.innerHTML = `
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    <span class="truncate">${file.name}</span>
                    <span class="text-xs text-gray-500 flex-shrink-0">(${formatFileSize(file.size)})</span>
                </div>
                <button type="button" onclick="removeReplyFile(${index})" class="text-red-500 hover:text-red-700 ml-2 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            container.appendChild(fileItem);
        });
    }

    function removeReplyFile(index) {
        replySelectedFiles.splice(index, 1);
        updateReplyFileList();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function createMessageBubble({ author, content, date, isCurrentUser, attachments, userRole }) {
        const alignClass = isCurrentUser ? 'justify-end' : 'justify-start';
        const bubbleClass = isCurrentUser ? 'bg-indigo-600 text-white' : 'bg-white text-gray-900 border border-gray-200';
        const authorClass = isCurrentUser ? 'text-right' : 'text-left';
        
        const showMessage = content !== '[Attachment]';
        
        let attachmentsHtml = '';
        if (attachments && attachments.length > 0) {
            attachmentsHtml = `
                <div class="mt-3 space-y-2">
                    ${attachments.map(att => createAttachmentElement(att, isCurrentUser)).join('')}
                </div>
            `;
        }

        let rolePrefix = '';
        // Show role prefix for non-customers (include current user so agent sees [Agent] prefix)
        if (userRole && userRole !== 'customer') {
            const roleColors = {
                'admin': 'text-red-600',
                'manager': 'text-blue-600',
                'agent': 'text-green-600'
            };
            const roleClass = roleColors[userRole] || 'text-gray-600';
            const roleText = userRole.charAt(0).toUpperCase() + userRole.slice(1);
            rolePrefix = `<span class="text-xs ${roleClass}">[${roleText}]</span> `;
        }

        return `
            <div class="flex ${alignClass}">
                <div class="max-w-md">
                    <p class="text-xs text-gray-500 mb-1 ${authorClass}">${rolePrefix}${author} • ${date}</p>
                    <div class="${bubbleClass} rounded-lg px-4 py-3 shadow-sm">
                        ${showMessage ? `<p class="text-sm whitespace-pre-wrap">${content}</p>` : ''}
                        ${attachmentsHtml}
                    </div>
                </div>
            </div>
        `;
    }

    function createSystemMessage({ content, date, author, isCurrentUser, userRole, userName }) {
        const alignClass = isCurrentUser ? 'justify-end' : 'justify-start';
        const authorClass = isCurrentUser ? 'text-right' : 'text-left';
        
        // Add colored role prefix
        let rolePrefix = '';
        if (userRole) {
            const roleColors = {
                'customer': 'text-purple-600',
                'admin': 'text-red-600',
                'manager': 'text-blue-600',
                'agent': 'text-green-600'
            };
            const roleClass = roleColors[userRole] || 'text-gray-600';
            const roleText = userRole.charAt(0).toUpperCase() + userRole.slice(1);
            rolePrefix = `<span class="${roleClass}">[${roleText}]</span> `;
        }
        
        return `
            <div class="flex ${alignClass} my-4">
                <div class="max-w-lg">
                    <p class="text-xs text-gray-500 mb-1 ${authorClass}">${rolePrefix}${author} • ${date}</p>
                    <div class="bg-orange-50 border-l-4 border-orange-500 rounded-r-lg px-4 py-3 shadow-sm">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-orange-900">${content}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function createReopenRequestBubble(reopenReq, currentUserId, ticketId) {
        const isCurrentUser = reopenReq.requested_by_id === currentUserId;
        const alignClass = isCurrentUser ? 'justify-end' : 'justify-start';
        const authorClass = isCurrentUser ? 'text-right' : 'text-left';
        
        let statusBadge = '';
        let actionButtons = '';
        let pendingInfo = '';
        let bubbleColor = 'yellow'; // Default for pending
        
        // Role color mapping
        const roleColors = {
            'admin': 'text-red-600',
            'manager': 'text-blue-600',
            'agent': 'text-green-600',
            'customer': 'text-purple-600'
        };
        
        // Build requester role prefix
        const reqRole = reopenReq.requested_by_role || 'customer';
        const reqRoleClass = roleColors[reqRole] || 'text-gray-600';
        const reqRoleText = reqRole.charAt(0).toUpperCase() + reqRole.slice(1);
        const requesterPrefix = `<span class="${reqRoleClass}">[${reqRoleText}]</span> `;
        
        // Determine status badge and color (map database enum values to display)
        if (reopenReq.status === 'pending') {
            bubbleColor = 'yellow';
            statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending Review</span>';
            
            // Show approve/reject buttons for agents (but not for the requester)
            const userRole = '{{ auth()->user()->role ?? "" }}';
            if (userRole === 'agent' && !isCurrentUser) {
                actionButtons = `
                    <div class="mt-3 flex gap-2">
                        <button onclick="showRemarksModal(${ticketId}, ${reopenReq.id}, 'approved')" 
                                class="px-3 py-1 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700">
                            Approve
                        </button>
                        <button onclick="showRemarksModal(${ticketId}, ${reopenReq.id}, 'rejected')" 
                                class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700">
                            Reject
                        </button>
                    </div>
                `;
            }
            
            // Add pending info section for context
            pendingInfo = `
                <div class="mt-2 p-2 bg-yellow-100 bg-opacity-50 rounded text-xs">
                    <p class="text-yellow-800"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Awaiting review by Admin or Manager</p>
                    <p class="text-yellow-700 mt-1">Submitted: ${reopenReq.requested_at_formatted || 'Recently'}</p>
                </div>
            `;
        } else if (reopenReq.status === 'accepted') {
            bubbleColor = 'green';
            statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>';
        } else if (reopenReq.status === 'declined') {
            bubbleColor = 'red';
            statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>';
        }
        
        // Dynamic color classes based on status
        let bgColorClass, borderColorClass, iconColorClass, textColorClass;
        if (bubbleColor === 'green') {
            bgColorClass = 'bg-green-50';
            borderColorClass = 'border-green-500';
            iconColorClass = 'text-green-500';
            textColorClass = 'text-green-900';
        } else if (bubbleColor === 'red') {
            bgColorClass = 'bg-red-50';
            borderColorClass = 'border-red-500';
            iconColorClass = 'text-red-500';
            textColorClass = 'text-red-900';
        } else {
            bgColorClass = 'bg-yellow-50';
            borderColorClass = 'border-yellow-500';
            iconColorClass = 'text-yellow-500';
            textColorClass = 'text-yellow-900';
        }
        
        // Build response section for approved/rejected
        let responseSection = '';
        if (reopenReq.status !== 'pending' && reopenReq.responded_by_name) {
            const respRole = reopenReq.responded_by_role || 'staff';
            const respRoleClass = roleColors[respRole] || 'text-gray-600';
            const respRoleText = respRole.charAt(0).toUpperCase() + respRole.slice(1);
            const respRolePrefix = `<span class="${respRoleClass}">[${respRoleText}]</span> `;
            
            const actionText = reopenReq.status === 'accepted' ? 'Approved' : 'Rejected';
            const actionIcon = reopenReq.status === 'accepted' 
                ? '<svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                : '<svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
            
            responseSection = `
                <div class="mt-3 pt-3 border-t ${bubbleColor === 'green' ? 'border-green-200' : 'border-red-200'}">
                    <p class="text-xs font-medium ${textColorClass} mb-1">
                        ${actionIcon}<strong>${actionText} by ${respRolePrefix}${reopenReq.responded_by_name}</strong>
                    </p>
                    <p class="text-xs ${textColorClass} opacity-80">on ${reopenReq.responded_at_formatted || 'Recently'}</p>
                    ${reopenReq.remarks ? `
                        <div class="mt-2 p-2 bg-white bg-opacity-70 rounded">
                            <p class="text-xs font-semibold ${textColorClass}">Remarks:</p>
                            <p class="text-xs ${textColorClass} opacity-90">${reopenReq.remarks}</p>
                        </div>
                    ` : ''}
                </div>
            `;
        }
        
        return `
            <div class="flex ${alignClass} my-4">
                <div class="max-w-lg w-full">
                    <p class="text-xs text-gray-500 mb-1 ${authorClass}">
                        ${requesterPrefix}${reopenReq.requested_by_name || 'User'} • ${reopenReq.requested_at_formatted || 'Recently'}
                    </p>
                    <div class="${bgColorClass} border-l-4 ${borderColorClass} rounded-r-lg px-4 py-3 shadow-sm">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 ${iconColorClass} mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <p class="text-sm font-semibold ${textColorClass}">Reopen Request</p>
                                    ${statusBadge}
                                </div>
                                <p class="text-sm ${textColorClass}"><strong>Reason:</strong> ${reopenReq.reason || 'No reason provided'}</p>
                                ${pendingInfo}
                                ${responseSection}
                                ${actionButtons}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function showRemarksModal(ticketId, reopenRequestId, action) {
        const modal = document.getElementById('remarksModal');
        const actionText = action === 'approved' ? 'Approve' : 'Reject';
        
        document.getElementById('remarksModalTitle').textContent = `${actionText} Reopen Request`;
        document.getElementById('remarksModalAction').textContent = actionText;
        document.getElementById('remarksTextarea').value = '';
        
        document.getElementById('remarksModalAction').onclick = function() {
            handleReopenRequest(ticketId, reopenRequestId, action);
        };
        
        modal.classList.remove('hidden');
    }

    function closeRemarksModal() {
        document.getElementById('remarksModal').classList.add('hidden');
    }

    function handleReopenRequest(ticketId, reopenRequestId, action) {
        const remarks = document.getElementById('remarksTextarea').value;
        
        fetch(`/agent/tickets/${ticketId}/reopen-request/${reopenRequestId}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ remarks: remarks })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeRemarksModal();
                alert(data.message);
                loadTicketDetails(ticketId);
            } else {
                alert(data.message || `Error ${action === 'approved' ? 'approving' : 'rejecting'} reopen request`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Error ${action === 'approved' ? 'approving' : 'rejecting'} reopen request. Please try again.`);
        });
    }

    // Re-Escalation Modal Functions
    function showReEscalationModal(ticketId, escalationId) {
        const modal = document.getElementById('reEscalationModal');
        document.getElementById('reEscalationReason').value = '';
        
        document.getElementById('reEscalationModalSubmit').onclick = function() {
            submitReEscalation(ticketId, escalationId);
        };
        
        modal.classList.remove('hidden');
    }

    function closeReEscalationModal() {
        document.getElementById('reEscalationModal').classList.add('hidden');
    }

    function submitReEscalation(ticketId, escalationId) {
        const reason = document.getElementById('reEscalationReason').value.trim();
        
        if (reason.length < 10) {
            alert('Please provide a detailed reason (at least 10 characters).');
            return;
        }
        
        fetch(`/agent/tickets/${ticketId}/request-re-escalation`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeReEscalationModal();
                alert(data.message);
                loadTicketDetails(ticketId);
                // Update table row status if needed
                if (data.status_name && data.status_color) {
                    updateTableRowStatus(ticketId, data.status_name, data.status_color);
                }
            } else {
                alert(data.message || 'Error requesting re-escalation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error requesting re-escalation. Please try again.');
        });
    }

    // Agent Reopen Request Modal Functions
    function showAgentReopenModal(ticketId) {
        const modal = document.getElementById('agentReopenModal');
        document.getElementById('agentReopenReason').value = '';
        
        document.getElementById('agentReopenModalSubmit').onclick = function() {
            submitAgentReopenRequest(ticketId);
        };
        
        modal.classList.remove('hidden');
    }

    function closeAgentReopenModal() {
        document.getElementById('agentReopenModal').classList.add('hidden');
    }

    function submitAgentReopenRequest(ticketId) {
        const reason = document.getElementById('agentReopenReason').value.trim();
        
        if (reason.length < 10) {
            alert('Please provide a detailed reason (at least 10 characters).');
            return;
        }
        
        fetch(`/agent/tickets/${ticketId}/request-reopen`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeAgentReopenModal();
                alert(data.message);
                loadTicketDetails(ticketId);
            } else {
                alert(data.message || 'Error requesting reopen');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error requesting reopen. Please try again.');
        });
    }

    function createEscalationBubble(escalation, currentUserId, ticketId, ticketStatus, allEscalations) {
        const isCurrentUser = escalation.requested_by_id === currentUserId;
        const alignClass = isCurrentUser ? 'justify-end' : 'justify-start';
        const authorClass = isCurrentUser ? 'text-right' : 'text-left';
        
        let statusBadge = '';
        let messageContent = '';
        let actionButton = '';
        let bubbleColor = 'orange'; // Default color
        
        // Check if ticket is closed
        const isClosed = ticketStatus && ticketStatus.toLowerCase() === 'closed';
        
        // Check if escalation was rejected
        const isRejected = escalation.resolution_notes && escalation.resolution_notes.startsWith('REJECTED:');
        
        // Check if there's a newer escalation from current user (for rejected escalations)
        // For rejected escalations: any newer escalation means "Already Requested"
        const hasNewerEscalation = allEscalations && allEscalations.some(esc => 
            esc.requested_at > escalation.requested_at && 
            esc.requested_by_id === currentUserId
        );
        
        // Check if there's a pending re-escalation (for resolved escalations)
        // Compare with the ORIGINAL escalation requester, not current user
        // A re-escalation means someone requested escalation AFTER this one was resolved
        const hasPendingReEscalation = allEscalations && allEscalations.some(esc => 
            esc.requested_at > escalation.resolved_at && 
            esc.requested_by_id === escalation.requested_by_id
        );
        
        // Scenario 1: Escalated to Admin (Out of agent's hands)
        if (escalation.escalated_by_id && !escalation.resolved_by_id) {
            statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Escalated</span>';
            // Hide escalator name only if role is agent (shouldn't happen), otherwise show actual name
            const escalatedByRole = escalation.escalated_by_role && escalation.escalated_by_role !== 'agent'
                ? `[${escalation.escalated_by_role.charAt(0).toUpperCase() + escalation.escalated_by_role.slice(1)}] ` 
                : '[Manager] ';
            const escalatedByName = escalation.escalated_by_role && escalation.escalated_by_role !== 'agent'
                ? (escalation.escalated_by_name || 'Manager')
                : 'Manager';
            messageContent = `
                <p class="text-sm text-orange-900 mb-2">
                    ✓ Your escalation request was approved by ${escalatedByRole}${escalatedByName} on ${escalation.escalated_at_formatted || 'recently'}.
                </p>
                <p class="text-sm text-orange-800 font-medium">
                    This ticket has been escalated to Admin for resolution.
                </p>
                <p class="text-sm text-orange-700 mt-2">
                    You'll be notified when the admin resolves the issue and it's ready for your verification.
                </p>
            `;
            // No action button - out of their hands
        } 
        // Scenario 2b: Escalation Rejected - Agent can re-escalate
        else if (escalation.resolved_by_id && isRejected) {
            bubbleColor = 'red';
            statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>';
            const resolvedByRole = escalation.resolved_by_role ? `[${escalation.resolved_by_role.charAt(0).toUpperCase() + escalation.resolved_by_role.slice(1)}] ` : '';
            const rejectionNotes = escalation.resolution_notes.replace('REJECTED:', '').trim();
            messageContent = `
                <p class="text-sm text-red-900 mb-2">
                    Escalation rejected by ${resolvedByRole}${escalation.resolved_by_name || 'Admin'}.
                </p>
                <p class="text-sm text-red-800">
                    The escalation request was declined. You can request re-escalation if needed.
                </p>
                ${rejectionNotes ? `
                    <div class="mt-2 p-2 bg-white bg-opacity-70 rounded">
                        <p class="text-xs font-semibold text-red-900 mb-1">Rejection Reason:</p>
                        <p class="text-xs text-red-800">${rejectionNotes}</p>
                    </div>
                ` : ''}
            `;
            
            // For rejected escalations, check if there's ANY newer escalation
            if (hasNewerEscalation) {
                actionButton = `
                    <div class="mt-3">
                        <span class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-200 rounded cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Already Requested
                        </span>
                    </div>
                `;
            } else {
                actionButton = `
                    <div class="mt-3 flex gap-2">
                        <button onclick="showReEscalationModal(${ticketId}, ${escalation.id})" 
                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-white bg-orange-600 rounded hover:bg-orange-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Request Re-Escalation
                        </button>
                    </div>
                `;
            }
        }
        // Scenario 2c: Escalation Resolved - Check if verified, failed, or pending verification
        else if (escalation.resolved_by_id) {
            // Check if there's a pending re-escalation (resolution didn't work)
            if (hasPendingReEscalation) {
                bubbleColor = 'red';
                statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Resolution Did Not Work</span>';
                const resolvedByRole = escalation.resolved_by_role ? `[${escalation.resolved_by_role.charAt(0).toUpperCase() + escalation.resolved_by_role.slice(1)}] ` : '';
                messageContent = `
                    <p class="text-sm text-red-900 mb-2">
                        Escalation was resolved by ${resolvedByRole}${escalation.resolved_by_name || 'Admin'}, but the issue persists.
                    </p>
                    <p class="text-sm text-red-800">
                        Agent has requested re-escalation. This resolution did not fix the problem.
                    </p>
                    ${escalation.resolution_notes ? `
                        <div class="mt-2 p-2 bg-white bg-opacity-70 rounded">
                            <p class="text-xs font-semibold text-red-900 mb-1">Previous Resolution Notes:</p>
                            <p class="text-xs text-red-800">${escalation.resolution_notes}</p>
                        </div>
                    ` : ''}
                `;
                
                actionButton = `
                    <div class="mt-3">
                        <span class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-200 rounded cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Already Requested
                        </span>
                    </div>
                `;
            } 
            // Check if verified (no newer escalations AND ticket is closed)
            else if (!hasPendingReEscalation && isClosed) {
                bubbleColor = 'green';
                statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Verified</span>';
                const resolvedByRole = escalation.resolved_by_role ? `[${escalation.resolved_by_role.charAt(0).toUpperCase() + escalation.resolved_by_role.slice(1)}] ` : '';
                messageContent = `
                    <p class="text-sm text-green-900 mb-2">
                        Escalation resolved by ${resolvedByRole}${escalation.resolved_by_name || 'Admin'}.
                    </p>
                    <p class="text-sm text-green-800 font-medium">
                        ✓ This escalation was verified and closed by Agent.
                    </p>
                    ${escalation.resolution_notes ? `
                        <div class="mt-2 p-2 bg-white bg-opacity-70 rounded">
                            <p class="text-xs font-semibold text-green-900 mb-1">Resolution Notes:</p>
                            <p class="text-xs text-green-800">${escalation.resolution_notes}</p>
                        </div>
                    ` : ''}
                `;
                // No action buttons - already verified and closed
            }
            // Pending verification (resolved but ticket not closed yet)
            else {
                bubbleColor = 'blue';
                statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Resolved</span>';
                const resolvedByRole = escalation.resolved_by_role ? `[${escalation.resolved_by_role.charAt(0).toUpperCase() + escalation.resolved_by_role.slice(1)}] ` : '';
                messageContent = `
                    <p class="text-sm text-blue-900 mb-2">
                        Escalation resolved by ${resolvedByRole}${escalation.resolved_by_name || 'Admin'}.
                    </p>
                    <p class="text-sm text-blue-800">
                        Pending for agent to verify. Please verify the fix with the customer and close the ticket, or request re-escalation if the issue persists.
                    </p>
                    ${escalation.resolution_notes ? `
                        <div class="mt-2 p-2 bg-white bg-opacity-70 rounded">
                            <p class="text-xs font-semibold text-blue-900 mb-1">Resolution Notes:</p>
                            <p class="text-xs text-blue-800">${escalation.resolution_notes}</p>
                        </div>
                    ` : ''}
                `;
                actionButton = `
                    <div class="mt-3 flex gap-2">
                        <button onclick="verifyAndCloseTicket(${ticketId})" 
                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Verify & Close Ticket
                        </button>
                        <button onclick="showReEscalationModal(${ticketId}, ${escalation.id})" 
                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-white bg-orange-600 rounded hover:bg-orange-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Request Re-Escalation
                        </button>
                    </div>
                `;
            }
        }
        // Scenario 3: Pending escalation (requested but not yet escalated)
        else {
            statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending Escalation</span>';
            messageContent = `
                <p class="text-sm text-orange-900 mb-2">
                    Escalation request submitted on ${escalation.requested_at_formatted || 'recently'}.
                </p>
                <p class="text-sm text-orange-800">
                    Waiting for manager review.
                </p>
            `;
        }
        
        // Build ticket details with dynamic colors
        let detailTextColor, reasonTextColor;
        if (bubbleColor === 'green') {
            detailTextColor = 'text-green-900';
            reasonTextColor = 'text-green-800';
        } else if (bubbleColor === 'red') {
            detailTextColor = 'text-red-900';
            reasonTextColor = 'text-red-800';
        } else if (bubbleColor === 'blue') {
            detailTextColor = 'text-blue-900';
            reasonTextColor = 'text-blue-800';
        } else {
            detailTextColor = 'text-orange-900';
            reasonTextColor = 'text-orange-800';
        }
        
        const detailsHtml = `
            <div class="space-y-2 text-xs ${detailTextColor} mb-3">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="font-semibold">Ticket #:</span>
                        <span class="ml-1">${escalation.ticket_number || 'N/A'}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Status:</span>
                        <span class="ml-1">${escalation.ticket_status || 'N/A'}</span>
                    </div>
                </div>
                <div>
                    <span class="font-semibold">Requested by:</span>
                    <span class="ml-1">${escalation.requested_by_role ? escalation.requested_by_role.charAt(0).toUpperCase() + escalation.requested_by_role.slice(1) : 'Staff'} - ${escalation.requested_by_name || 'Unknown'}</span>
                </div>
                <div>
                    <span class="font-semibold">Requested at:</span>
                    <span class="ml-1">${escalation.requested_at_formatted || 'Recently'}</span>
                </div>
            </div>
            <div class="p-2 bg-white bg-opacity-70 rounded mb-2">
                <p class="text-xs font-semibold ${detailTextColor} mb-1">Reason:</p>
                <p class="text-xs ${reasonTextColor}">${escalation.reason || 'No reason provided'}</p>
            </div>
        `;
        
        // Dynamic color classes based on bubble color
        let bgColorClass, borderColorClass, iconColorClass, textColorClass;
        if (bubbleColor === 'green') {
            bgColorClass = 'bg-green-50';
            borderColorClass = 'border-green-500';
            iconColorClass = 'text-green-500';
            textColorClass = 'text-green-900';
        } else if (bubbleColor === 'red') {
            bgColorClass = 'bg-red-50';
            borderColorClass = 'border-red-500';
            iconColorClass = 'text-red-500';
            textColorClass = 'text-red-900';
        } else if (bubbleColor === 'blue') {
            bgColorClass = 'bg-blue-50';
            borderColorClass = 'border-blue-500';
            iconColorClass = 'text-blue-500';
            textColorClass = 'text-blue-900';
        } else {
            bgColorClass = 'bg-orange-50';
            borderColorClass = 'border-orange-500';
            iconColorClass = 'text-orange-500';
            textColorClass = 'text-orange-900';
        }
        
        // Build author display with role and name (always show actual name, alignment handled by isCurrentUser)
        const roleColors = {
            'admin': 'text-red-600',
            'manager': 'text-purple-600',
            'agent': 'text-green-600',
            'customer': 'text-blue-600'
        };
        const role = escalation.requested_by_role || 'staff';
        const roleClass = roleColors[role] || 'text-gray-600';
        const roleText = role.charAt(0).toUpperCase() + role.slice(1);
        const requesterName = escalation.requested_by_name || 'Unknown';
        const escalationAuthor = `<span class="${roleClass} font-semibold">[${roleText}]</span> ${requesterName}`;
        
        return `
            <div class="flex ${alignClass} my-4">
                <div class="max-w-2xl w-full">
                    <p class="text-xs text-gray-500 mb-1 ${authorClass}">${escalationAuthor} • ${escalation.requested_at_formatted || 'Recently'}</p>
                    <div class="${bgColorClass} border-l-4 ${borderColorClass} rounded-r-lg px-4 py-3 shadow-sm">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 ${iconColorClass} mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-3">
                                    <p class="text-sm font-semibold ${textColorClass}">Escalation</p>
                                    ${statusBadge}
                                </div>
                                ${detailsHtml}
                                ${messageContent}
                                ${actionButton}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function verifyAndCloseTicket(ticketId) {
        if (!confirm('Have you verified the fix with the customer? This will close the ticket.')) {
            return;
        }
        
        fetch(`/agent/tickets/${ticketId}/verify-and-close`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Server error response:', text);
                    throw new Error(`Server error: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadTicketDetails(ticketId);
                // Update table row status
                if (data.status_name && data.status_color) {
                    updateTableRowStatus(ticketId, data.status_name, data.status_color);
                }
            } else {
                console.error('Error details:', data);
                alert(data.message || 'Error closing ticket');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error closing ticket. Check console for details.');
        });
    }

    function createAttachmentElement(attachment, isCurrentUser) {
        const fileExtension = attachment.file_name.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension);
        const isPdf = fileExtension === 'pdf';
        const isDoc = ['doc', 'docx'].includes(fileExtension);
        
        const textColorClass = isCurrentUser ? 'text-indigo-200 hover:text-white' : 'text-indigo-600 hover:text-indigo-800';
        const bgClass = isCurrentUser ? 'bg-white bg-opacity-20' : 'bg-gray-50';
        const borderClass = isCurrentUser ? 'border-white border-opacity-30' : 'border-gray-200';
        
        if (isImage) {
            return `
                <div class="attachment-item">
                    <div class="block ${textColorClass} cursor-pointer" onclick="openImageModal('${attachment.file_path}', '${attachment.file_name}'); event.preventDefault();">
                        <div class="${bgClass} rounded-md p-2 border ${borderClass}">
                            <img src="/tickets/attachment/${attachment.file_path}" 
                                 alt="${attachment.file_name}"
                                 class="max-w-full h-auto max-h-32 rounded object-cover hover:opacity-80 transition-opacity"
                                 style="max-width: 200px;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div style="display: none;" class="flex items-center gap-2 text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>${attachment.file_name}</span>
                            </div>
                            <p class="text-xs mt-1 text-center opacity-80">${attachment.file_name}</p>
                        </div>
                    </div>
                </div>
            `;
        } else if (isPdf) {
            return `
                <div class="attachment-item">
                    <a href="/tickets/attachment/${attachment.file_path}" target="_blank" class="flex items-center gap-2 text-xs ${textColorClass} ${bgClass} rounded-md p-2 border ${borderClass} hover:opacity-80 transition-all">
                        <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 18h12V6l-4-4H4v16zm6-10V4l4 4h-4z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="truncate font-medium">${attachment.file_name}</p>
                            <p class="text-xs opacity-75">${formatFileSize(attachment.file_size)}</p>
                        </div>
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                </div>
            `;
        } else if (isDoc) {
            return `
                <div class="attachment-item">
                    <a href="/tickets/attachment/${attachment.file_path}" target="_blank" class="flex items-center gap-2 text-xs ${textColorClass} ${bgClass} rounded-md p-2 border ${borderClass} hover:opacity-80 transition-all">
                        <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 18h12V6l-4-4H4v16zm6-10V4l4 4h-4z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="truncate font-medium">${attachment.file_name}</p>
                            <p class="text-xs opacity-75">${formatFileSize(attachment.file_size)}</p>
                        </div>
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                </div>
            `;
        } else {
            return `
                <div class="attachment-item">
                    <a href="/tickets/attachment/${attachment.file_path}" target="_blank" class="flex items-center gap-2 text-xs ${textColorClass} ${bgClass} rounded-md p-2 border ${borderClass} hover:opacity-80 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="truncate font-medium">${attachment.file_name}</p>
                            <p class="text-xs opacity-75">${formatFileSize(attachment.file_size)}</p>
                        </div>
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                </div>
            `;
        }
    }

    function openImageModal(imagePath, fileName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="max-w-6xl max-h-full w-full flex flex-col">
                <div class="bg-white rounded-t-lg px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 truncate">${fileName}</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="/tickets/attachment/${imagePath}" download="${fileName}" 
                           class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                           title="Download">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                        <button onclick="this.closest('.fixed').remove()" 
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="bg-black rounded-b-lg overflow-auto flex items-center justify-center" style="max-height: calc(90vh - 70px);">
                    <img src="/tickets/attachment/${imagePath}" 
                         alt="${fileName}" 
                         class="max-w-full h-auto object-contain"
                         style="max-height: calc(90vh - 100px);">
                </div>
            </div>
        `;
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
        
        const closeHandler = function(e) {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', closeHandler);
            }
        };
        document.addEventListener('keydown', closeHandler);
        
        document.body.appendChild(modal);
    }
</script>
