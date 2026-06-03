@extends('layouts.admin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')
@section('page-icon', 'bi-pencil-square')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-pencil-square"></i>
                    Edit: {{ $category->name }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('categories.update', $category) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Optional description...">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Update Category
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
