<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">All Users</h3>
                        <p class="text-sm text-gray-500 mt-1">Manage user accounts and roles</p>
                    </div>

                    <!-- Filters and Search -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Search -->
                                <div class="lg:col-span-2">
                                    <label for="search" class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                           placeholder="Search by name or email..."
                                           class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <!-- State Filter -->
                                <div>
                                    <label for="status" class="block text-xs font-medium text-gray-600 mb-1">State</label>
                                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">All State</option>
                                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="deactivated" {{ request('status') === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                                    </select>
                                </div>

                                <!-- Role Filter -->
                                <div>
                                    <label for="role" class="block text-xs font-medium text-gray-600 mb-1">Role</label>
                                    <select name="role" id="role" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">All Roles</option>
                                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                                        <option value="agent" {{ request('role') === 'agent' ? 'selected' : '' }}>Agent</option>
                                        <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </div>

                                <!-- Sort By -->
                                <div>
                                    <label for="sort_by" class="block text-xs font-medium text-gray-600 mb-1">Sort By</label>
                                    <select name="sort_by" id="sort_by" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Date Joined</option>
                                        <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                                        <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email (A-Z)</option>
                                        <option value="id" {{ request('sort_by') === 'id' ? 'selected' : '' }}>ID</option>
                                    </select>
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label for="sort_order" class="block text-xs font-medium text-gray-600 mb-1">Order</label>
                                    <select name="sort_order" id="sort_order" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                                        <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                                    </select>
                                </div>

                                <!-- Show + Buttons -->
                                <div class="lg:col-span-2 flex gap-2 items-end">
                                    <div class="flex-1">
                                        <label for="per_page" class="block text-xs font-medium text-gray-600 mb-1">Show</label>
                                        <select name="per_page" id="per_page" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 entries</option>
                                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 entries</option>
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 entries</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 entries</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        Apply Filters
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Results Info -->
                    <div class="mb-4 flex justify-between items-center text-sm text-gray-600">
                        <div>
                            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        State
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">
                                        Joined
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr class="@if($loop->even) bg-gray-50 @endif hover:bg-indigo-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700 font-medium">
                                            {{ $user->id }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500 md:hidden">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center hidden md:table-cell">
                                            <div class="text-sm text-gray-600">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-xs font-semibold text-gray-700">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($user->deleted_at)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Deactivated
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 hidden lg:table-cell">
                                            {{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            @if($user->id === auth()->id())
                                                <!-- Current logged-in admin - no actions allowed -->
                                                <div class="flex items-center justify-center">
                                                    <span class="text-xs text-gray-500 italic">You (No actions)</span>
                                                </div>
                                            @else
                                                <div class="flex items-center justify-center gap-2">
                                                    <!-- Show Button (Blue) -->
                                                    <a href="{{ route('admin.users.show', ['id' => $user->id]) }}" 
                                                        class="inline-flex items-center p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-md transition-colors"
                                                        title="View User">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>

                                                    <!-- Edit Button -->
                                                    <a href="{{ route('admin.users.edit', ['id' => $user->id]) }}" 
                                                        class="inline-flex items-center p-2 text-yellow-600 hover:text-yellow-800 hover:bg-yellow-100 rounded-md transition-colors"
                                                        title="Edit User">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>

                                                    <!-- Deactivate Button (only for active users) -->
                                                    @if(!$user->deleted_at)
                                                    <form method="POST" action="{{ route('admin.users.destroy', ['id' => $user->id]) }}" 
                                                          onsubmit="return confirm('Are you sure you want to deactivate this user?');" 
                                                          class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="inline-flex items-center p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-md transition-colors"
                                                                title="Deactivate User">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="mt-4 font-medium text-gray-600">No users found</p>
                                            <p class="mt-1 text-gray-400">Try adjusting your search or filter criteria</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($users->hasPages())
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>