<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-users-cog text-indigo-600 mr-3"></i>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('User Management') }}
                </h2>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn-success">
                <i class="fas fa-user-plus mr-2"></i>
                Create New User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-error" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-list mr-2"></i>
                        All Users
                    </h3>
                </div>
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-header-cell">
                                        <i class="fas fa-user mr-2"></i>Name
                                    </th>
                                    <th class="table-header-cell">
                                        <i class="fas fa-envelope mr-2"></i>Email
                                    </th>
                                    <th class="table-header-cell">
                                        <i class="fas fa-key mr-2"></i>Password
                                    </th>
                                    <th class="table-header-cell">
                                        <i class="fas fa-id-badge mr-2"></i>Role
                                    </th>
                                    <th class="table-header-cell">
                                        <i class="fas fa-calendar mr-2"></i>Created
                                    </th>
                                    <th class="table-header-cell">
                                        <i class="fas fa-cog mr-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="table-cell">
                                            <div class="flex items-center">
                                                <a href="{{ route('admin.users.calendar', $user) }}" class="flex items-center group">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-200 transition">
                                                            <i class="fas fa-user text-indigo-600"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="font-medium text-gray-900 group-hover:text-indigo-700 transition">{{ $user->name }}</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="table-cell">
                                            <div class="text-gray-900">{{ $user->email }}</div>
                                        </td>
                                        <td class="table-cell">
                                            <span class="font-mono text-sm bg-gray-100 px-3 py-1 rounded border border-gray-300">
                                                <i class="fas fa-lock text-gray-400 mr-1"></i>
                                                {{ $user->initial_password ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="table-cell">
                                            @foreach($user->roles as $role)
                                                <span class="{{ $role->name === 'admin' ? 'badge-success' : 'badge-info' }}">
                                                    <i class="fas {{ $role->name === 'admin' ? 'fa-shield-alt' : 'fa-user' }} mr-1"></i>
                                                    {{ ucfirst($role->name) }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="table-cell text-gray-500">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ $user->created_at->format('Y-m-d') }}
                                        </td>
                                        <td class="table-cell">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 transition">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Edit
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                                            <i class="fas fa-trash mr-1"></i>
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center">
                                            <div class="text-gray-500">
                                                <i class="fas fa-users text-4xl mb-2"></i>
                                                <p>No users found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
