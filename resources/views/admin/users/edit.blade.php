<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <i class="fas fa-user-edit text-indigo-600 mr-3"></i>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit User') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-edit mr-2"></i>
                        Update User Information
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Name -->
                        <div class="input-group">
                            <x-input-label for="name" :value="__('Name')" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <x-text-input id="name" class="block mt-1 w-full pl-10" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="input-group">
                            <x-input-label for="email" :value="__('Email')" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <x-text-input id="email" class="block mt-1 w-full pl-10" type="email" name="email" :value="old('email', $user->email)" required />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="input-group">
                            <x-input-label for="role" :value="__('Role')" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-badge text-gray-400"></i>
                                </div>
                                <select id="role" name="role" class="form-input pl-10">
                                    <option value="user" {{ old('role', $user->roles->first()?->name) === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ old('role', $user->roles->first()?->name) === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Current Password Display -->
                        @if($user->initial_password)
                        <div class="input-group">
                            <x-input-label for="current_password" :value="__('Current Password')" />
                            <div class="flex items-center mt-1 w-full p-3 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-300 rounded-md">
                                <i class="fas fa-lock text-indigo-500 mr-2"></i>
                                <span class="font-mono text-sm font-semibold text-gray-700">{{ $user->initial_password }}</span>
                            </div>
                        </div>
                        @endif

                        <!-- New Password (Optional) -->
                        <div class="input-group">
                            <x-input-label for="new_password" :value="__('New Password (leave blank to keep current)')" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-key text-gray-400"></i>
                                </div>
                                <x-text-input id="new_password" class="block mt-1 w-full pl-10" type="text" name="new_password" :value="old('new_password')" />
                            </div>
                            <x-input-error :messages="$errors->get('new_password')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-1"></i>
                                Enter a new password to reset the user's password
                            </p>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('admin.users') }}" class="btn-secondary">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>

                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                {{ __('Update User') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
