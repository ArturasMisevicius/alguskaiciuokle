<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <i class="fas fa-home text-indigo-600 mr-3"></i>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-hand-wave mr-2"></i>
                        Welcome, {{ $user->name }}!
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-gray-600">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i>
                        You are logged in as a regular user.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Information Card -->
                <div class="stat-card">
                    <div class="stat-card-body">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                    <i class="fas fa-user-circle text-2xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Your Information</h4>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-user w-6 text-blue-500"></i>
                                <span class="font-medium mr-2">Name:</span>
                                <span>{{ $user->name }}</span>
                            </div>
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-envelope w-6 text-blue-500"></i>
                                <span class="font-medium mr-2">Email:</span>
                                <span>{{ $user->email }}</span>
                            </div>
                            <div class="flex items-center text-gray-700">
                                <i class="far fa-calendar-alt w-6 text-blue-500"></i>
                                <span class="font-medium mr-2">Member Since:</span>
                                <span>{{ $user->created_at->format('F j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="stat-card">
                    <div class="stat-card-body">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                                    <i class="fas fa-bolt text-2xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Quick Actions</h4>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <a href="{{ route('user.profile') }}" class="flex items-center text-purple-600 hover:text-purple-800 transition">
                                <i class="fas fa-user-edit w-6"></i>
                                <span class="hover:underline">Edit Profile</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center text-purple-600 hover:text-purple-800 transition">
                                <i class="fas fa-cog w-6"></i>
                                <span class="hover:underline">Account Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
