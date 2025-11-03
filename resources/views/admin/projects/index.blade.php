<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-project-diagram text-indigo-600 mr-3"></i>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Project Management') }}
                </h2>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn-success">
                <i class="fas fa-plus mr-2"></i>
                Create Project
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
                        Projects
                    </h3>
                </div>
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-header-cell">Name</th>
                                    <th class="table-header-cell">Company</th>
                                    <th class="table-header-cell">Code</th>
                                    <th class="table-header-cell">Active</th>
                                    <th class="table-header-cell">Timesheets</th>
                                    <th class="table-header-cell">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                                @forelse($projects as $project)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="table-cell">{{ $project->name }}</td>
                                        <td class="table-cell">{{ optional($project->company)->name }}</td>
                                        <td class="table-cell">{{ $project->code ?? '—' }}</td>
                                        <td class="table-cell">
                                            @if($project->is_active)
                                                <span class="badge-success"><i class="fas fa-check mr-1"></i> Active</span>
                                            @else
                                                <span class="badge-secondary"><i class="fas fa-minus mr-1"></i> Inactive</span>
                                            @endif
                                        </td>
                                        <td class="table-cell">{{ $project->timesheets_count }}</td>
                                        <td class="table-cell">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 transition">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-project-diagram text-4xl mb-2"></i>
                                            <p>No projects found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



