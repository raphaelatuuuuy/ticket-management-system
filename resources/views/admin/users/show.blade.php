<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-6">
                        <!-- User ID -->
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-medium text-gray-900">User Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ID -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">User ID</label>
                                <p class="mt-1 text-sm text-gray-900">#{{ $user->id }}</p>
                            </div>

                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Role</label>
                                <div class="mt-1">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($user->role === 'manager') bg-purple-100 text-purple-800
                                        @elseif($user->role === 'agent') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Account Status</label>
                                <div class="mt-1">
                                    @if($user->deleted_at)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Deactivated
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">Deactivated on {{ $user->deleted_at->format('M d, Y H:i') }}</p>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Email Verified -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($user->email_verified_at)
                                        <span class="text-green-600">✓ Verified on {{ $user->email_verified_at->format('M d, Y H:i') }}</span>
                                    @else
                                        <span class="text-red-600">✗ Not verified</span>
                                    @endif
                                </p>
                            </div>

                            <!-- Created At -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Joined</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $user->created_at ? $user->created_at->format('M d, Y H:i') : 'N/A' }}
                                </p>
                            </div>

                            <!-- Updated At -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $user->updated_at ? $user->updated_at->format('M d, Y H:i') : 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="border-t border-gray-200 pt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.users.edit', ['id' => $user->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Edit User
                            </a>
                            @if(!$user->deleted_at)
                            <form method="POST" action="{{ route('admin.users.destroy', ['id' => $user->id]) }}" 
                                  onsubmit="return confirm('Are you sure you want to deactivate this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                    Deactivate User
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>