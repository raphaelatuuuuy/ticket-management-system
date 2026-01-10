<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Tickets') }}
            </h2>
            <button onclick="openTicketWindow()" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Submit a Ticket
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg onclick="this.parentElement.parentElement.style.display='none'" class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg onclick="this.parentElement.parentElement.style.display='none'" class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            <!-- Search and Filter Controls -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <!-- Search Bar -->
                    <div class="flex-1 w-full">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="searchInput"
                                placeholder="Search by subject or ticket ID..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                                onkeyup="filterTickets()"
                            >
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Status Filter Dropdown -->
                    <div class="w-full sm:w-48">
                        <select 
                            id="statusFilter"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm bg-white"
                            onchange="filterTickets()"
                        >
                            <option value="">All Status</option>
                            @php
                                $statuses = \App\Models\Status::where('description', '!=', 'Escalated')->orderBy('description')->get();
                            @endphp
                            @foreach($statuses as $statusOption)
                                <option value="{{ strtolower($statusOption->description) }}">{{ $statusOption->description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @php
                    $tickets = \App\Models\Ticket::where('user_id', auth()->id())
                        ->whereNull('deleted_at')
                        ->orderBy('updated_at', 'desc')
                        ->get();
                @endphp
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ticket ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Last Updated
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody id="ticketsTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse($tickets as $ticket)
                                <tr class="ticket-row hover:bg-gray-50 cursor-pointer transition-colors" 
                                    data-ticket-id="{{ $ticket->id }}"
                                    data-title="{{ $ticket->title }}"
                                    data-status="{{ strtolower($ticket->status) }}"
                                    onclick="openTicketDetail({{ $ticket->id }})">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-indigo-600">#{{ $ticket->ticket_number }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">{{ Str::limit($ticket->title, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-500">
                                            @php
                                                $date = $ticket->updated_at->setTimezone('Asia/Manila');
                                                $now = now()->setTimezone('Asia/Manila');
                                                if ($date->isToday()) {
                                                    echo 'Today, ' . $date->format('g:i A');
                                                } elseif ($date->isYesterday()) {
                                                    echo 'Yesterday, ' . $date->format('g:i A');
                                                } elseif ($date->year === $now->year) {
                                                    echo $date->format('F j, g:i A');
                                                } else {
                                                    echo $date->format('F j, Y');
                                                }
                                            @endphp
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusDisplay = $ticket->status_relation ? $ticket->status_relation->description : 'Pending';
                                            $statusColor = $ticket->status_relation ? $ticket->status_relation->color : '#6b7280';
                                            // Show 'In Progress' instead of 'Escalated' to customers
                                            if ($statusDisplay === 'Escalated') {
                                                $statusDisplay = 'In Progress';
                                                $statusColor = '#3b82f6'; // Blue color for In Progress
                                            }
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                              style="background-color: {{ $statusColor }}20; color: {{ $statusColor }}">
                                            {{ $statusDisplay }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noTicketsRow">
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No tickets yet</h3>
                                        <p class="mt-1 text-sm text-gray-500">Have concerns? Create a new support ticket. We're here to help you!</p>
                                        <div class="mt-6">
                                            <button onclick="openTicketWindow()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                Submit a Ticket
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Ticket Window Overlay -->
    <div id="ticketWindowOverlay" class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-40 hidden" onclick="closeTicketWindow()"></div>
    
    <!-- Submit Ticket Window -->
    <div id="ticketWindow" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Submit a Ticket') }}
                    </h2>
                    <button onclick="closeTicketWindow()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Title (was Subject) -->
                    <div class="mb-4">
                        <label for="title" class="block font-medium text-sm text-gray-700">
                            Title <span class="text-red-600">*</span>
                        </label>
                        <input id="title" type="text" name="title" value="{{ old('title') }}" required 
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block font-medium text-sm text-gray-700">
                            Description <span class="text-red-600">*</span>
                        </label>
                        <textarea id="description" name="description" rows="6" required 
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachments with Drag and Drop -->
                    <div class="mb-4">
                        <label for="attachments" class="block font-medium text-sm text-gray-700 mb-2">
                            Attachments
                        </label>
                        
                        <!-- Drag and Drop Zone -->
                        <div id="dropZone" 
                             class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">
                                <span class="font-semibold text-indigo-600">Click to upload</span> or drag and drop
                            </p>
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, PDF, DOC up to 10MB each</p>
                            <input id="attachments" type="file" name="attachments[]" multiple class="hidden" onchange="handleFiles(this.files)">
                        </div>

                        <!-- File List -->
                        <div id="fileList" class="mt-3 space-y-2"></div>
                        
                        @error('attachments')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('attachments.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Window Actions -->
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" onclick="closeTicketWindow()" 
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                        <div id="replySection" class="border-t border-gray-200 p-4 bg-gray-50">
                            <!-- Closed Ticket Notice -->
                            <div id="closedTicketNotice" class="hidden mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm text-yellow-700 font-medium">This ticket has been closed.</p>
                                        <p class="text-sm text-yellow-600 mt-1">If you still have concerns or new issues have arisen, you can request to reopen this ticket or submit a new one.</p>
                                        <div class="mt-3 flex gap-2">
                                            <button onclick="openReopenRequestModal()" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-md text-sm transition-colors">
                                                Request to Reopen
                                            </button>
                                            <button onclick="openTicketWindow()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm transition-colors">
                                                Submit New Ticket
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reopen Request Pending Notice -->
                            <div id="reopenPendingNotice" class="hidden mb-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm text-blue-700 font-medium">Your reopen request is pending review.</p>
                                        <p class="text-sm text-blue-600 mt-1">An admin or manager will review your request shortly.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Reply Form -->
                            <form id="replyForm" onsubmit="submitReply(event)">
                                <input type="hidden" id="replyTicketId" name="ticket_id">
                                
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
                                        <input id="replyAttachments" type="file" name="attachments[]" multiple class="hidden" onchange="handleReplyFiles(this.files)">
                                    </div>
                                    <div id="replyFileList" class="mt-2 space-y-1"></div>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button type="submit" 
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm">
                                        Send Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reopen Request Modal -->
    <div id="reopenRequestModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Request to Reopen Ticket</h3>
                    <button onclick="closeReopenRequestModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="reopenRequestForm" onsubmit="submitReopenRequest(event)">
                    <div class="mb-4">
                        <label for="reopenReason" class="block text-sm font-medium text-gray-700 mb-2">
                            Please provide a reason for reopening this ticket <span class="text-red-600">*</span>
                        </label>
                        <textarea id="reopenReason" name="reason" rows="4" required
                            placeholder="Explain why you need this ticket reopened..."
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeReopenRequestModal()" 
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md text-sm">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-md text-sm">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Ticket window
        function openTicketWindow(){
            document.getElementById('ticketWindowOverlay').classList.remove('hidden');
            document.getElementById('ticketWindow').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeTicketWindow(){
            document.getElementById('ticketWindowOverlay').classList.add('hidden');
            document.getElementById('ticketWindow').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Slide-over detail
        function openTicketDetail(ticketId){
            document.getElementById('ticketDetailOverlay').classList.remove('hidden');
            document.getElementById('ticketDetailPanel').classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
            loadTicketDetails(ticketId);
        }
        function closeTicketDetail(){
            document.getElementById('ticketDetailOverlay').classList.add('hidden');
            document.getElementById('ticketDetailPanel').classList.add('translate-x-full');
            document.body.style.overflow = 'auto';
        }

        // Filter tickets in the list by search text (subject or ticket id) and status
        function filterTickets(){
            const q = (document.getElementById('searchInput')?.value || '').trim().toLowerCase();
            const status = (document.getElementById('statusFilter')?.value || '').toLowerCase();
            const rows = document.querySelectorAll('.ticket-row');
            rows.forEach(r => {
                const title = (r.dataset.title || '').toLowerCase();
                const rowStatus = (r.dataset.status || '').toLowerCase();
                const firstCellText = (r.querySelector('td')?.textContent || '').toLowerCase();

                let matchesQuery = true;
                if (q) {
                    matchesQuery = title.includes(q) || firstCellText.includes(q) || String(r.dataset.ticketId || '').includes(q);
                }

                let matchesStatus = true;
                if (status) matchesStatus = rowStatus === status;

                if (matchesQuery && matchesStatus) r.style.display = ''; else r.style.display = 'none';
            });
        }

        // Load ticket details and render conversation
        function loadTicketDetails(ticketId){
            const conv = document.getElementById('conversationThread');
            if (conv) {
                conv.innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
            }

            fetch(`/tickets/${ticketId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const setText = (id, value) => { const el = document.getElementById(id); if (el) el.textContent = value; };
                const setHtml = (id, html) => { const el = document.getElementById(id); if (el) el.innerHTML = html; };

                const statusHtml = `<span class="px-2 py-1 text-xs font-semibold rounded-full" style="background-color:${data.status_color || '#6b7280'}20; color:${data.status_color || '#6b7280'}">${data.status || 'N/A'}</span>`;

                setText('detailTicketId', '#' + (data.ticket_number || ticketId));
                setHtml('detailTicketStatus', statusHtml);
                setText('sidebarTicketId', '#' + (data.ticket_number || ticketId));
                setText('sidebarCreated', data.created_at_formatted || '');
                setText('sidebarLastActivity', data.updated_at_formatted || '');
                setHtml('sidebarStatus', statusHtml);
                setText('detailTitle', data.title || '');

                const currentUserId = {{ auth()->id() }};

                // Build conversation using bubble/timeline logic
                let conversationHtml = '';

                // Treat original description as customer's own message (align right)
                conversationHtml += createMessageBubble({
                    author: data.customer_name || 'Customer',
                    content: data.description || '',
                    date: data.created_at_formatted || '',
                    isCurrentUser: true,
                    attachments: data.attachments || [],
                    userRole: 'customer'
                });

                // Build timeline from reopen requests and comments
                const timeline = [];

                if (data.reopenRequests && Array.isArray(data.reopenRequests)) {
                    data.reopenRequests.forEach(rr => timeline.push({ type: 'reopen_request', timestamp: rr.requested_at, data: rr }));
                } else if (data.reopen_request) {
                    timeline.push({ type: 'reopen_request', timestamp: data.reopen_request.requested_at, data: data.reopen_request });
                }

                if (data.comments && Array.isArray(data.comments)) {
                    data.comments.forEach(c => timeline.push({ type: 'comment', timestamp: c.created_at, data: c }));
                }

                // Sort by timestamp
                timeline.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

                timeline.forEach(item => {
                    if (item.type === 'reopen_request') {
                        conversationHtml += createReopenRequestBubble(item.data, currentUserId, data.id);
                    } else if (item.type === 'comment') {
                        const comment = item.data;
                        const isCurrentUserComment = comment.user_id === currentUserId;
                        conversationHtml += createMessageBubble({
                            author: comment.user_name || 'User',
                            content: comment.content,
                            date: comment.created_at_formatted || '',
                            isCurrentUser: isCurrentUserComment,
                            attachments: comment.attachments || [],
                            userRole: comment.user_role || null
                        });
                    }
                });

                if (conv) conv.innerHTML = conversationHtml;

                // Reply form (rendered dynamically; disabled for closed/resolved tickets)
                const reply = document.getElementById('replySection');
                if (reply) {
                    const ticketIdVal = data.id || ticketId;
                    const statusVal = String(data.status || '').toLowerCase();
                    const isClosed = (statusVal === 'closed' || statusVal === 'close');

                    if (isClosed) {
                        reply.innerHTML = `
                            <input type="hidden" id="replyTicketId" value="${ticketIdVal}">
                            <div class="border-t border-gray-200 bg-white py-2 px-3 flex items-center justify-between gap-3 max-h-20">
                                <div class="flex items-center gap-3 min-w-0">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">Ticket is closed.</p>
                                        <p class="text-xs text-gray-500 truncate">Still have concerns? You can request to reopen.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openReopenRequestModal()" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm">Request Reopen</button>
                                </div>
                            </div>
                        `;
                    } else {
                        reply.innerHTML = `
                            <form id="replyForm" onsubmit="submitReply(event)">
                                <input type="hidden" id="replyTicketId" name="ticket_id" value="${ticketIdVal}">
                                <div class="mb-3">
                                    <textarea id="replyMessage" name="message" rows="3" placeholder="Type your reply here..." class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"></textarea>
                                </div>
                                <div class="mb-3">
                                    <div id="replyDropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-3 text-center cursor-pointer transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50">
                                        <p class="text-sm text-gray-600"><span class="font-semibold text-indigo-600">Click to attach files</span> or drag and drop</p>
                                        <input id="replyAttachments" type="file" name="attachments[]" multiple class="hidden">
                                    </div>
                                    <div id="replyFileList" class="mt-2 space-y-1"></div>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm">Send Reply</button>
                                </div>
                            </form>
                        `;

                        // Initialize reply drop handlers
                        initializeReplyDropZone();
                    }
                }

                // Show reopen pending notice if present
                const pendingNotice = document.getElementById('reopenPendingNotice');
                if (pendingNotice) {
                    const anyPending = (data.reopenRequests && data.reopenRequests.some(r => r.status === 'pending')) || (data.reopen_request && data.reopen_request.status === 'pending');
                    if (anyPending) pendingNotice.classList.remove('hidden'); else pendingNotice.classList.add('hidden');
                }
            })
            .catch(err => {
                if (conv) conv.innerHTML = '<div class="text-center py-12 text-red-600">Error loading ticket details.</div>';
                console.error('Error loading ticket details:', err);
            });
        }

        // Reply submit (supports message-only, attachments-only, or both)
        function submitReply(e){
            e.preventDefault();
            const ticketId = document.getElementById('replyTicketId')?.value;
            const messageEl = document.getElementById('replyMessage');
            const message = messageEl?.value || '';

            // Allow attachment-only replies: require either message text or at least one selected file
            if (!message.trim() && (!replySelectedFiles || replySelectedFiles.length === 0)) {
                alert('Please enter a message or attach at least one file');
                return;
            }

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('message', message);

            if (replySelectedFiles && replySelectedFiles.length > 0) {
                replySelectedFiles.forEach(f => fd.append('attachments[]', f));
            }

            fetch(`/tickets/${ticketId}/reply`, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Clear reply inputs and file list
                        if (messageEl) messageEl.value = '';
                        replySelectedFiles = [];
                        updateReplyFileList();

                        loadTicketDetails(ticketId);
                    } else {
                        alert(data.message || 'Error sending reply');
                    }
                })
                .catch(err => { console.error(err); alert('Error sending reply'); });
        }

        // File handling for submit ticket (basic list display)
        function handleFiles(files){
            const list = document.getElementById('fileList');
            if (!list) return;
            list.innerHTML = '';
            Array.from(files).forEach(f => {
                const el = document.createElement('div');
                el.className = 'flex items-center justify-between bg-gray-50 border px-3 py-2 rounded';
                el.textContent = f.name + ' (' + Math.round(f.size/1024) + ' KB)';
                list.appendChild(el);
            });
        }

        // Reply attachments are handled by the richer `handleReplyFiles` and `replySelectedFiles` utilities below

        // Reopen request modal
        function openReopenRequestModal(){
            document.getElementById('reopenRequestModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeReopenRequestModal(){
            document.getElementById('reopenRequestModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Submit reopen request
        function submitReopenRequest(e){
            e.preventDefault();
            const reason = document.getElementById('reopenReason')?.value || '';
            if (!reason.trim()) { alert('Please provide a reason'); return; }

            // ticket id should be from current open detail (if available)
            const ticketId = document.getElementById('replyTicketId')?.value || null;
            if (!ticketId) { alert('No ticket selected'); return; }

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('ticket_id', ticketId);
            fd.append('reason', reason);

            fetch(`/tickets/${ticketId}/request-reopen`, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        closeReopenRequestModal();
                        alert(data.message || 'Reopen request submitted');
                        loadTicketDetails(ticketId);
                    } else {
                        alert(data.message || 'Error submitting request');
                    }
                })
                .catch(err => { console.error(err); alert('Error submitting request'); });
        }

        // Drag & drop hookup for the submit ticket zone
        (function attachDragHandlers(){
            const drop = document.getElementById('dropZone');
            if (!drop) return;
            drop.addEventListener('click', () => document.getElementById('attachments').click());
            drop.addEventListener('dragover', (e) => { e.preventDefault(); drop.classList.add('bg-indigo-50'); });
            drop.addEventListener('dragleave', () => drop.classList.remove('bg-indigo-50'));
            drop.addEventListener('drop', (e) => {
                e.preventDefault(); drop.classList.remove('bg-indigo-50');
                const files = e.dataTransfer.files; if (files && files.length) { document.getElementById('attachments').files = files; handleFiles(files); }
            });

            const replyDrop = document.getElementById('replyDropZone');
            if (!replyDrop) return;
            replyDrop.addEventListener('click', () => document.getElementById('replyAttachments').click());
            replyDrop.addEventListener('dragover', (e) => { e.preventDefault(); replyDrop.classList.add('bg-indigo-50'); });
            replyDrop.addEventListener('dragleave', () => replyDrop.classList.remove('bg-indigo-50'));
            replyDrop.addEventListener('drop', (e) => {
                e.preventDefault(); replyDrop.classList.remove('bg-indigo-50');
                const files = e.dataTransfer.files; if (files && files.length) { document.getElementById('replyAttachments').files = files; handleReplyFiles(files); }
            });
        })();

        let replySelectedFiles = [];

        function initializeReplyDropZone() {
            const replyDropZone = document.getElementById('replyDropZone');
            const replyFileInput = document.getElementById('replyAttachments');
            
            if (!replyDropZone || !replyFileInput) return;

            // Remove old event listeners by cloning
            const newDropZone = replyDropZone.cloneNode(true);
            replyDropZone.parentNode.replaceChild(newDropZone, replyDropZone);
            
            const newFileInput = replyFileInput.cloneNode(true);
            replyFileInput.parentNode.replaceChild(newFileInput, replyFileInput);

            // Add new event listeners
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

            // Role badge
            let rolePrefix = '';
            if (userRole && userRole !== 'customer' && !isCurrentUser) {
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
            
            // Determine status badge and color
            if (reopenReq.status === 'pending') {
                bubbleColor = 'yellow';
                statusBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending Review</span>';
                
                const userRole = '{{ auth()->user()->role ?? "" }}';
                if ((userRole === 'admin' || userRole === 'manager') && !isCurrentUser) {
                    actionButtons = `
                        <div class="mt-3 flex gap-2">
                            <button onclick="showRemarksModal(${ticketId}, ${reopenReq.id}, 'approved')" class="px-3 py-1 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700">Approve</button>
                            <button onclick="showRemarksModal(${ticketId}, ${reopenReq.id}, 'rejected')" class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700">Reject</button>
                        </div>
                    `;
                }
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
            
            let bgColorClass, borderColorClass, iconColorClass, textColorClass;
            if (bubbleColor === 'green') {
                bgColorClass = 'bg-green-50'; borderColorClass = 'border-green-500'; iconColorClass = 'text-green-500'; textColorClass = 'text-green-900';
            } else if (bubbleColor === 'red') {
                bgColorClass = 'bg-red-50'; borderColorClass = 'border-red-500'; iconColorClass = 'text-red-500'; textColorClass = 'text-red-900';
            } else {
                bgColorClass = 'bg-yellow-50'; borderColorClass = 'border-yellow-500'; iconColorClass = 'text-yellow-500'; textColorClass = 'text-yellow-900';
            }
            
            let responseSection = '';
            if (reopenReq.status !== 'pending' && reopenReq.responded_by_name) {
                const respRole = reopenReq.responded_by_role || 'staff';
                const respRoleClass = roleColors[respRole] || 'text-gray-600';
                const respRoleText = respRole.charAt(0).toUpperCase() + respRole.slice(1);
                const respRolePrefix = `<span class="${respRoleClass}">[${respRoleText}]</span> `;
                const actionText = reopenReq.status === 'accepted' ? 'Approved' : 'Rejected';
                const actionIcon = reopenReq.status === 'accepted' ? '<svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' : '<svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
                responseSection = `
                    <div class="mt-3 pt-3 border-t ${bubbleColor === 'green' ? 'border-green-200' : 'border-red-200'}">
                        <p class="text-xs font-medium ${textColorClass} mb-1">${actionIcon}<strong>${actionText} by ${respRolePrefix}${reopenReq.responded_by_name}</strong></p>
                        <p class="text-xs ${textColorClass} opacity-80">on ${reopenReq.responded_at_formatted || 'Recently'}</p>
                        ${reopenReq.remarks ? `<div class="mt-2 p-2 bg-white bg-opacity-70 rounded"><p class="text-xs font-semibold ${textColorClass}">Remarks:</p><p class="text-xs ${textColorClass} opacity-90">${reopenReq.remarks}</p></div>` : ''}
                    </div>
                `;
            }

            return `
                <div class="flex ${alignClass} my-4">
                    <div class="max-w-lg w-full">
                        <p class="text-xs text-gray-500 mb-1 ${authorClass}">${requesterPrefix}${reopenReq.requested_by_name || 'User'} • ${reopenReq.requested_at_formatted || 'Recently'}</p>
                        <div class="${bgColorClass} border-l-4 ${borderColorClass} rounded-r-lg px-4 py-3 shadow-sm">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 ${iconColorClass} mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
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
                                <img src="/tickets/attachment/${attachment.file_path}" alt="${attachment.file_name}" class="max-w-full h-auto max-h-32 rounded object-cover hover:opacity-80 transition-opacity" style="max-width: 200px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="display: none;" class="flex items-center gap-2 text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
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
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6l-4-4H4v16zm6-10V4l4 4h-4z"/></svg>
                            <div class="flex-1 min-w-0"><p class="truncate font-medium">${attachment.file_name}</p><p class="text-xs opacity-75">${formatFileSize(attachment.file_size)}</p></div>
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </a>
                    </div>
                `;
            } else if (isDoc) {
                return `
                    <div class="attachment-item">
                        <a href="/tickets/attachment/${attachment.file_path}" target="_blank" class="flex items-center gap-2 text-xs ${textColorClass} ${bgClass} rounded-md p-2 border ${borderClass} hover:opacity-80 transition-all">
                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6l-4-4H4v16zm6-10V4l4 4h-4z"/></svg>
                            <div class="flex-1 min-w-0"><p class="truncate font-medium">${attachment.file_name}</p><p class="text-xs opacity-75">${formatFileSize(attachment.file_size)}</p></div>
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </a>
                    </div>
                `;
            } else {
                return `
                    <div class="attachment-item">
                        <a href="/tickets/attachment/${attachment.file_path}" target="_blank" class="flex items-center gap-2 text-xs ${textColorClass} ${bgClass} rounded-md p-2 border ${borderClass} hover:opacity-80 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                            <div class="flex-1 min-w-0"><p class="truncate font-medium">${attachment.file_name}</p><p class="text-xs opacity-75">${formatFileSize(attachment.file_size)}</p></div>
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
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
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <h3 class="text-lg font-medium text-gray-900 truncate">${fileName}</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="/tickets/attachment/${imagePath}" download="${fileName}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="Download">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            </a>
                            <button onclick="this.closest('.fixed').remove()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="bg-black rounded-b-lg overflow-auto flex items-center justify-center" style="max-height: calc(90vh - 70px);">
                        <img src="/tickets/attachment/${imagePath}" alt="${fileName}" class="max-w-full h-auto object-contain" style="max-height: calc(90vh - 100px);">
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

        // Resize handle: allow dragging the left edge to resize the slide-over
        function setupResizeHandle() {
            const panel = document.getElementById('ticketDetailPanel');
            const handle = document.getElementById('resizeHandle');
            if (!panel || !handle) return;

            let isDragging = false;
            let startX = 0;
            let startWidth = 0;

            const minWidth = () => Math.round(window.innerWidth * 0.25);
            const maxWidth = () => window.innerWidth; // full viewport

            const onPointerDown = (e) => {
                isDragging = true;
                startX = (e.touches ? e.touches[0].clientX : e.clientX);
                startWidth = panel.offsetWidth;
                document.documentElement.style.cursor = 'ew-resize';
                document.body.style.userSelect = 'none';
                window.addEventListener('mousemove', onPointerMove);
                window.addEventListener('touchmove', onPointerMove, { passive: false });
                window.addEventListener('mouseup', onPointerUp);
                window.addEventListener('touchend', onPointerUp);
            };

            const onPointerMove = (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const clientX = (e.touches ? e.touches[0].clientX : e.clientX);
                const delta = startX - clientX; // moving left increases width
                let newWidth = startWidth + delta;
                const minW = minWidth();
                const maxW = maxWidth();
                if (newWidth < minW) newWidth = minW;
                if (newWidth > maxW) newWidth = maxW;
                panel.style.width = newWidth + 'px';
            };

            const onPointerUp = () => {
                if (!isDragging) return;
                isDragging = false;
                document.documentElement.style.cursor = '';
                document.body.style.userSelect = '';
                window.removeEventListener('mousemove', onPointerMove);
                window.removeEventListener('touchmove', onPointerMove);
                window.removeEventListener('mouseup', onPointerUp);
                window.removeEventListener('touchend', onPointerUp);
            };

            handle.addEventListener('mousedown', onPointerDown);
            handle.addEventListener('touchstart', onPointerDown, { passive: true });

            // Double-click to toggle full width
            handle.addEventListener('dblclick', (e) => {
                const currentWidth = panel.getBoundingClientRect().width;
                const full = Math.round(window.innerWidth);
                if (Math.abs(currentWidth - full) > 4) {
                    panel.style.width = full + 'px';
                } else {
                    panel.style.width = Math.round(window.innerWidth * 0.5) + 'px';
                }
            });

            // Respect viewport changes
            window.addEventListener('resize', () => {
                const cur = panel.getBoundingClientRect().width;
                const minW = minWidth();
                if (cur < minW) panel.style.width = minW + 'px';
            });
        }

        // Initialize resize handle
        setupResizeHandle();
    </script>

</x-app-layout>