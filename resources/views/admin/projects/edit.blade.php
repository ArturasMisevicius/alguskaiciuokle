<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-project-diagram text-indigo-600 mr-3"></i>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Project') }}
                </h2>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-edit mr-2"></i>
                        Update Project
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.projects.update', $project) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="company_id" class="form-label">Company</label>
                            <select id="company_id" name="company_id" class="form-input" required>
                                <option value="">Select company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" @selected(old('company_id', $project->company_id) == $company->id)>{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="name" class="form-label">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $project->name) }}" class="form-input" required>
                            @error('name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="code" class="form-label">Code (optional)</label>
                            <input id="code" name="code" type="text" value="{{ old('code', $project->code) }}" class="form-input">
                            @error('code')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="form-label">Description (optional)</label>
                            <textarea id="description" name="description" class="form-input" rows="4">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" class="mr-2" @checked(old('is_active', $project->is_active))>
                            <label for="is_active" class="form-label mb-0">Active</label>
                            @error('is_active')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.projects.index') }}" class="btn-secondary">Cancel</a>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



