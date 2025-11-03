<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-building text-indigo-600 mr-3"></i>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Create Company') }}
                </h2>
            </div>
            <a href="{{ route('admin.companies.index') }}" class="btn-secondary">
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
                        <i class="fas fa-plus mr-2"></i>
                        New Company
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.companies.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="name" class="form-label">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-input" required>
                            @error('name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="code" class="form-label">Code (optional)</label>
                            <input id="code" name="code" type="text" value="{{ old('code') }}" class="form-input">
                            @error('code')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.companies.index') }}" class="btn-secondary">Cancel</a>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


