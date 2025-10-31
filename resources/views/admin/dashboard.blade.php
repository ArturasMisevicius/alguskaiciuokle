<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <i class="fas fa-tachometer-alt text-indigo-600 mr-3"></i>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-user-shield mr-2"></i>
                        Welcome, {{ Auth::user()->name }}!
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-gray-600">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i>
                        You are logged in as an administrator with full system access.
                    </p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Users Card -->
                <div class="stat-card">
                    <div class="stat-card-body">
                        <div class="flex items-center">
                            <div class="stat-icon">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Users
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            {{ $totalUsers }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Administrators Card -->
                <div class="stat-card">
                    <div class="stat-card-body">
                        <div class="flex items-center">
                            <div class="stat-icon">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                                    <i class="fas fa-user-shield text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Administrators
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            {{ $totalAdmins }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Regular Users Card -->
                <div class="stat-card">
                    <div class="stat-card-body">
                        <div class="flex items-center">
                            <div class="stat-icon">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                                    <i class="fas fa-user text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Regular Users
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            {{ $totalRegularUsers }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.users') }}" class="btn-primary">
                            <i class="fas fa-users-cog mr-2"></i>
                            Manage Users
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="btn-success">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create New User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
