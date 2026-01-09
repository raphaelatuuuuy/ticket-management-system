<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- System Oversight Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">System Oversight</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Users Widget -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-12 w-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                        <dd class="text-3xl font-semibold text-gray-900">{{ $totalUsers }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Tickets Widget -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Tickets</dt>
                                        <dd class="text-3xl font-semibold text-gray-900">{{ $totalTickets }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Role Distribution Widget -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg md:col-span-2">
                        <div class="p-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-4">User Role Distribution</h4>
                            <div class="space-y-3">
                                @foreach($usersByRole as $roleData)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                @if($roleData->role === 'admin') bg-purple-100 text-purple-800
                                                @elseif($roleData->role === 'manager') bg-blue-100 text-blue-800
                                                @elseif($roleData->role === 'agent') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($roleData->role) }}
                                            </span>
                                        </div>
                                        <span class="text-2xl font-semibold text-gray-900">{{ $roleData->total }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets by Status Section -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Tickets by Status</h3>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($ticketsByStatus as $statusData)
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  style="background-color: {{ $statusData->color }}20; color: {{ $statusData->color }}">
                                                {{ $statusData->description }}
                                            </span>
                                        </div>
                                        <span class="text-3xl font-bold text-gray-900">{{ $statusData->total }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full" 
                                                 style="width: {{ $totalTickets > 0 ? ($statusData->total / $totalTickets * 100) : 0 }}%; background-color: {{ $statusData->color }}">
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $totalTickets > 0 ? number_format(($statusData->total / $totalTickets * 100), 1) : 0 }}% of total
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
