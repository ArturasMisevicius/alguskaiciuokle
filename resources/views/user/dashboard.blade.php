<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Welcome, {{ $user->name }}!</h3>
                    <p class="mb-4">You are logged in as a regular user.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="bg-blue-100 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-blue-800 mb-2">Your Information</h4>
                            <p class="text-blue-600"><strong>Name:</strong> {{ $user->name }}</p>
                            <p class="text-blue-600"><strong>Email:</strong> {{ $user->email }}</p>
                            <p class="text-blue-600"><strong>Member Since:</strong> {{ $user->created_at->format('F j, Y') }}</p>
                        </div>
                        <div class="bg-purple-100 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-purple-800 mb-2">Quick Actions</h4>
                            <ul class="list-disc list-inside text-purple-600">
                                <li><a href="{{ route('user.profile') }}" class="hover:underline">Edit Profile</a></li>
                                <li><a href="{{ route('profile.edit') }}" class="hover:underline">Account Settings</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
