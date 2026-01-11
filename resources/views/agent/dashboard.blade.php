<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agent Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h3>
                    <p class="text-gray-600">"Excellence is not a skill, it's an attitude. Keep delivering outstanding support!"</p>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">My Open Tickets</p>
                                <p class="text-3xl font-bold text-blue-600">{{ $myAssignedTickets }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Closed Tickets</p>
                                <p class="text-3xl font-bold text-green-600">{{ $myClosedTickets }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Urgent Tickets</p>
                                <p class="text-3xl font-bold text-red-600">{{ $myUrgentTickets }}</p>
                            </div>
                            <div class="p-3 bg-red-100 rounded-full">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Required Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">My Pending Escalations</h4>
                            <span class="px-3 py-1 text-sm font-semibold text-orange-800 bg-orange-100 rounded-full">{{ $myPendingEscalations }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Awaiting manager/admin approval</p>
                        <button onclick="openPendingEscalationsModal()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All →</button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Approved Escalations</h4>
                            <span class="px-3 py-1 text-sm font-semibold text-blue-800 bg-blue-100 rounded-full">{{ $myApprovedEscalations }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Escalations in progress</p>
                        <button onclick="openApprovedEscalationsModal()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All →</button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">My Reopen Requests</h4>
                            <span class="px-3 py-1 text-sm font-semibold text-purple-800 bg-purple-100 rounded-full">{{ $myPendingReopenRequests }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Awaiting approval to reopen tickets</p>
                        <button onclick="openReopenRequestsModal()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All →</button>
                    </div>
                </div>
            </div>

            <!-- Charts and Distribution -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- My Tickets by Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">My Tickets by Status</h4>
                        <div class="space-y-3">
                            @forelse($myTicketsByStatus as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $item['color'] }}"></div>
                                    <span class="text-sm font-medium text-gray-700">{{ $item['status'] }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $item['count'] }}</span>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">No tickets assigned yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- My Tickets by Priority -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">My Tickets by Priority</h4>
                        <div class="space-y-3">
                            @forelse($myTicketsByPriority as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $item['color'] }}"></div>
                                    <span class="text-sm font-medium text-gray-700 capitalize">{{ $item['priority'] }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $item['count'] }}</span>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">No open tickets</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Tickets by Category -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">My Tickets by Category</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($myTicketsByCategory as $item)
                        <div class="flex items-center justify-between border rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $item['color'] }}"></div>
                                <span class="text-sm font-medium text-gray-700">{{ $item['category'] }}</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ $item['count'] }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500 italic">No tickets assigned yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Task Lists -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Urgent Tickets -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">My Urgent Tickets</h4>
                        <div class="space-y-3">
                            @forelse($urgentTicketsList as $ticket)
                            <div class="border-l-4 border-red-500 pl-3 py-2 bg-red-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <a href="{{ route('agent.tickets.index', ['filter' => 'urgent']) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                            #{{ $ticket->ticket_number }} - {{ Str::limit($ticket->title, 40) }}
                                        </a>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Customer: {{ $ticket->user->name }} | 
                                            {{ $ticket->status_relation->description ?? 'Status Not Set' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">No urgent tickets</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recently Assigned -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Recently Assigned</h4>
                        <div class="space-y-3">
                            @forelse($recentlyAssignedTickets as $ticket)
                            <div class="border-l-4 border-blue-500 pl-3 py-2 bg-blue-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <a href="{{ route('agent.tickets.index', ['filter' => 'recent']) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                            #{{ $ticket->ticket_number }} - {{ Str::limit($ticket->title, 40) }}
                                        </a>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ $ticket->user->name ?? 'Unknown' }} | 
                                            <span class="capitalize">{{ $ticket->priority_relation->description ?? 'No Priority Set' }}</span> | 
                                            {{ $ticket->updated_at ? $ticket->updated_at->diffForHumans() : 'Unknown' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">No tickets assigned</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- My Pending Escalations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">My Escalation Requests</h4>
                        <div class="space-y-3">
                            @forelse($myEscalationsList as $escalation)
                            <div class="border-l-4 border-orange-500 pl-3 py-2 bg-orange-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <a href="{{ route('agent.tickets.index', ['filter' => 'my_escalations']) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                            #{{ optional($escalation->ticket)->ticket_number ?? 'N/A' }} - {{ Str::limit(optional($escalation->ticket)->title ?? 'Ticket removed', 35) }}
                                        </a>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Status: Pending | 
                                            {{ $escalation->requested_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">No pending escalations</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Escalations Modal -->
    <div id="pendingEscalationsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">My Pending Escalations</h3>
                    <button onclick="closePendingEscalationsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($myPendingEscalationsList as $escalation)
                    <div class="border-l-4 border-orange-500 bg-orange-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">#{{ optional($escalation->ticket)->ticket_number ?? 'N/A' }} - {{ optional($escalation->ticket)->title ?? 'Ticket removed' }}</h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    Customer: {{ optional(optional($escalation->ticket)->user)->name ?? 'Unknown' }} | 
                                    Priority: {{ optional(optional($escalation->ticket)->priority_relation)->description ?? 'No Priority Set' }} | 
                                    Requested: {{ $escalation->requested_at ? $escalation->requested_at->diffForHumans() : 'Unknown' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($escalation->reason, 100) }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 italic text-center py-4">No pending escalations</p>
                    @endforelse
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('agent.tickets.index', ['filter' => 'my_escalations']) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                        Go to Support Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Approved Escalations Modal -->
    <div id="approvedEscalationsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">My Approved Escalations</h3>
                    <button onclick="closeApprovedEscalationsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($myApprovedEscalationsList as $escalation)
                    <div class="border-l-4 border-blue-500 bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">#{{ optional($escalation->ticket)->ticket_number ?? 'N/A' }} - {{ optional($escalation->ticket)->title ?? 'Ticket removed' }}</h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    Customer: {{ optional(optional($escalation->ticket)->user)->name ?? 'Unknown' }} | 
                                    Priority: {{ optional(optional($escalation->ticket)->priority_relation)->description ?? 'No Priority Set' }} | 
                                    Approved: {{ $escalation->escalated_at ? $escalation->escalated_at->diffForHumans() : 'Pending' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($escalation->reason, 100) }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 italic text-center py-4">No approved escalations</p>
                    @endforelse
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('agent.tickets.index', ['filter' => 'my_escalations']) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                        Go to Support Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reopen Requests Modal -->
    <div id="reopenRequestsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">My Reopen Requests</h3>
                    <button onclick="closeReopenRequestsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($myReopenRequestsList as $reopenRequest)
                    <div class="border-l-4 border-purple-500 bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">#{{ optional($reopenRequest->ticket)->ticket_number ?? 'N/A' }} - {{ optional($reopenRequest->ticket)->title ?? 'Ticket removed' }}</h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    Customer: {{ optional(optional($reopenRequest->ticket)->user)->name ?? 'Unknown' }} |
                                    Priority: {{ optional(optional($reopenRequest->ticket)->priority_relation)->description ?? 'No Priority Set' }} |
                                    Status: <span class="capitalize {{ $reopenRequest->status === 'pending' ? 'text-orange-600' : ($reopenRequest->status === 'accepted' ? 'text-green-600' : 'text-red-600') }}">{{ $reopenRequest->status }}</span> |
                                    Requested: {{ $reopenRequest->requested_at ? $reopenRequest->requested_at->diffForHumans() : 'Unknown' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($reopenRequest->reason, 100) }}</p>
                                @if($reopenRequest->status !== 'pending' && $reopenRequest->remarks)
                                <p class="text-sm text-gray-600 mt-1"><strong>Response:</strong> {{ Str::limit($reopenRequest->remarks, 100) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 italic text-center py-4">No reopen requests</p>
                    @endforelse
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('agent.tickets.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                        Go to Support Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPendingEscalationsModal() {
            document.getElementById('pendingEscalationsModal').classList.remove('hidden');
        }

        function closePendingEscalationsModal() {
            document.getElementById('pendingEscalationsModal').classList.add('hidden');
        }

        function openApprovedEscalationsModal() {
            document.getElementById('approvedEscalationsModal').classList.remove('hidden');
        }

        function closeApprovedEscalationsModal() {
            document.getElementById('approvedEscalationsModal').classList.add('hidden');
        }

        function openReopenRequestsModal() {
            document.getElementById('reopenRequestsModal').classList.remove('hidden');
        }

        function closeReopenRequestsModal() {
            document.getElementById('reopenRequestsModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const pendingModal = document.getElementById('pendingEscalationsModal');
            const approvedModal = document.getElementById('approvedEscalationsModal');
            const reopenModal = document.getElementById('reopenRequestsModal');
            
            if (event.target === pendingModal) {
                closePendingEscalationsModal();
            }
            if (event.target === approvedModal) {
                closeApprovedEscalationsModal();
            }
            if (event.target === reopenModal) {
                closeReopenRequestsModal();
            }
        }
    </script>
</x-app-layout>
