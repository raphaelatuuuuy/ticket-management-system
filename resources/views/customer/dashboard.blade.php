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
    
    <div id="ticketDetailPanel" class="fixed inset-y-0 right-0 bg-white shadow-xl z-50 transform translate-x-full transition-all duration-300 ease-in-out" style="width: 50%; min-width: 400px; max-width: 100%;">
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


</x-app-layout>