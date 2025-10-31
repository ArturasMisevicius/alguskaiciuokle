<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="mb-4">You are logged in as an administrator.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <div class="bg-blue-100 p-6 rounded-lg">
                            <h4 class="text-2xl font-bold text-blue-800">{{ $totalUsers }}</h4>
                            <p class="text-blue-600">Total Users</p>
                        </div>
                        <div class="bg-green-100 p-6 rounded-lg">
                            <h4 class="text-2xl font-bold text-green-800">{{ $totalAdmins }}</h4>
                            <p class="text-green-600">Administrators</p>
                        </div>
                        <div class="bg-purple-100 p-6 rounded-lg">
                            <h4 class="text-2xl font-bold text-purple-800">{{ $totalRegularUsers }}</h4>
                            <p class="text-purple-600">Regular Users</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('admin.users') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Manage Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
