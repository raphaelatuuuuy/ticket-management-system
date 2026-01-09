<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h3>
                    <p class="text-gray-600">Administrator Dashboard - Complete system overview and management</p>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Tickets</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $totalTickets }}</p>
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
                                <p class="text-sm font-medium text-gray-600">Open Tickets</p>
                                <p class="text-3xl font-bold text-green-600">{{ $openTickets }}</p>
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
                                <p class="text-3xl font-bold text-red-600">{{ $urgentTickets }}</p>
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
                            <h4 class="text-lg font-semibold text-gray-900">Pending Escalations</h4>
                            <span class="px-3 py-1 text-sm font-semibold text-orange-800 bg-orange-100 rounded-full">{{ $pendingEscalations }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Requires immediate attention</p>
                        <button onclick="openEscalationsModal()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All →</button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Reopen Requests</h4>
                            <span class="px-3 py-1 text-sm font-semibold text-purple-800 bg-purple-100 rounded-full">{{ $pendingReopenRequests }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Pending your approval</p>
                        <button onclick="openReopenRequestsModal()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All →</button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Total Users</h4>
                            <span class="px-3 py-1 text-sm font-semibold text-blue-800 bg-blue-100 rounded-full">{{ $totalUsers }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Agents: {{ $agentCount }} | Managers: {{ $managerCount }} | Customers: {{ $customerCount }}</p>
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Manage Users →</a>
                    </div>
                </div>
            </div>

            <!-- Charts and Distribution -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Tickets by Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Tickets by Status</h4>
                        <div class="space-y-3">
                            @foreach($ticketsByStatus as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $item['color'] }}"></div>
                                    <span class="text-sm font-medium text-gray-700">{{ $item['status'] }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $item['count'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Tickets by Priority -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Tickets by Priority</h4>
                        <div class="space-y-3">
                            @foreach($ticketsByPriority as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $item['color'] }}"></div>
                                    <span class="text-sm font-medium text-gray-700 capitalize">{{ $item['priority'] }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $item['count'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets by Category -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Tickets by Category</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($ticketsByCategory as $item)
                        <div class="flex items-center justify-between border rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $item['color'] }}"></div>
                                <span class="text-sm font-medium text-gray-700">{{ $item['category'] }}</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ $item['count'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Task Lists -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Urgent Tickets -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Urgent Tickets</h4>
                        <div class="space-y-3">
                            @forelse($urgentTicketsList as $ticket)
                            <div class="border-l-4 border-red-500 pl-3 py-2 bg-red-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <a href="{{ route('admin.tickets.index', ['filter' => 'urgent']) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                            #{{ $ticket->ticket_number }} - {{ Str::limit($ticket->title, 40) }}
                                        </a>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ $ticket->user->name }} | 
                                            @if($ticket->assignedTo)
                                                Assigned to: {{ $ticket->assignedTo->name }}
                                            @else
                                                <span class="text-red-600">Unassigned</span>
                                            @endif
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

                <!-- Unassigned Tickets -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Unassigned Tickets</h4>
                        <div class="space-y-3">
                            @forelse($unassignedTickets as $ticket)
                            <div class="border-l-4 border-yellow-500 pl-3 py-2 bg-yellow-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <a href="{{ route('admin.tickets.index', ['filter' => 'unassigned']) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                            #{{ $ticket->ticket_number }} - {{ Str::limit($ticket->title, 40) }}
                                        </a>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ $ticket->user->name }} | 
                                            <span class="capitalize">{{ $ticket->priority_relation->description ?? 'No Priority Set' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">All tickets assigned</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Pending Escalations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Pending Escalations</h4>
                        <div class="space-y-3">
                            @forelse($pendingEscalationsList as $escalation)
                            <div class="border-l-4 border-orange-500 pl-3 py-2 bg-orange-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <a href="{{ route('admin.tickets.index', ['filter' => 'pending_escalations']) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                            #{{ $escalation->ticket->ticket_number }} - {{ Str::limit($escalation->ticket->title, 35) }}
                                        </a>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Requested by: {{ $escalation->requestedBy->name }} | 
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

                <!-- Pending Reopen Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Pending Reopen Requests</h4>
                        <div class="space-y-3">
                            @forelse($pendingReopenRequestsList as $request)
                            <div class="border-l-4 border-purple-500 pl-3 py-2 bg-purple-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <a href="{{ route('admin.tickets.index', ['filter' => 'pending_reopens']) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                            #{{ $request->ticket->ticket_number }} - {{ Str::limit($request->ticket->title, 35) }}
                                        </a>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Requested by: {{ $request->requestedBy->name }} | 
                                            {{ $request->requested_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">No pending reopen requests</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Pending Escalations Modal -->
    <div id="escalationsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Pending Escalations ({{ $pendingEscalations }})</h3>
                <button onclick="closeEscalationsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($pendingEscalationsList as $escalation)
                <div class="border-l-4 border-orange-500 pl-4 py-3 mb-3 bg-orange-50 rounded">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-base font-semibold text-gray-900">
                                #{{ $escalation->ticket->ticket_number }} - {{ $escalation->ticket->title }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                Customer: {{ $escalation->ticket->user->name }}<br>
                                Requested by: {{ $escalation->requestedBy->name }} | {{ $escalation->requested_at->diffForHumans() }}<br>
                                Priority: <span class="capitalize">{{ $escalation->ticket->priority_relation->description ?? 'No Priority Set' }}</span>
                            </p>
                            @if($escalation->reason)
                            <p class="text-sm text-gray-700 mt-2"><strong>Reason:</strong> {{ $escalation->reason }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">No pending escalations</p>
                @endforelse
            </div>
            @if($pendingEscalationsList->count() > 0)
            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('admin.tickets.index', ['filter' => 'pending_escalations']) }}" class="w-full block text-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Go to Support Ticket
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Reopen Requests Modal -->
    <div id="reopenRequestsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Pending Reopen Requests ({{ $pendingReopenRequests }})</h3>
                <button onclick="closeReopenRequestsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($pendingReopenRequestsList as $request)
                <div class="border-l-4 border-purple-500 pl-4 py-3 mb-3 bg-purple-50 rounded">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-base font-semibold text-gray-900">
                                #{{ $request->ticket->ticket_number }} - {{ $request->ticket->title }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                Customer: {{ $request->ticket->user->name }}<br>
                                Requested by: {{ $request->requestedBy->name }} | {{ $request->requested_at->diffForHumans() }}
                            </p>
                            @if($request->reason)
                            <p class="text-sm text-gray-700 mt-2"><strong>Reason:</strong> {{ $request->reason }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">No pending reopen requests</p>
                @endforelse
            </div>
            @if($pendingReopenRequestsList->count() > 0)
            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('admin.tickets.index', ['filter' => 'pending_reopens']) }}" class="w-full block text-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Go to Support Ticket
                </a>
            </div>
            @endif
        </div>
    </div>

    <script>
        function openEscalationsModal() {
            document.getElementById('escalationsModal').classList.remove('hidden');
        }

        function closeEscalationsModal() {
            document.getElementById('escalationsModal').classList.add('hidden');
        }

        function openReopenRequestsModal() {
            document.getElementById('reopenRequestsModal').classList.remove('hidden');
        }

        function closeReopenRequestsModal() {
            document.getElementById('reopenRequestsModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const escalationsModal = document.getElementById('escalationsModal');
            const reopenModal = document.getElementById('reopenRequestsModal');
            if (event.target == escalationsModal) {
                closeEscalationsModal();
            }
            if (event.target == reopenModal) {
                closeReopenRequestsModal();
            }
        }
    </script>
</x-app-layout>
