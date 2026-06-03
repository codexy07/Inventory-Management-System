@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Product Categories')
@section('page-icon', 'bi-tags')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="section-title mb-0">
            <i class="bi bi-tags"></i>
            All Categories
            <span class="text-muted fw-normal small ms-2">({{ $categories->total() }})</span>
        </h5>
        @can('create')
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Category
        </a>
        @endcan
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th class="text-center">Products</th>
                        <th>Created</th>
                        @can('edit')<th style="width:100px;">Actions</th>@endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $category)
                        <tr>
                            <td class="text-muted">{{ $categories->firstItem() + $i }}</td>
                            <td class="fw-semibold">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge">
                                        <i class="bi bi-tag-fill"></i>
                                    </span>
                                    {{ $category->name }}
                                </div>
                            </td>
                            <td class="text-muted">
                                {{ $category->description ? Str::limit($category->description, 60) : '—' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-custom rounded-pill">
                                    {{ $category->products_count }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $category->created_at->format('M d, Y') }}
                            </td>
                            @can('edit')
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button onclick="confirmDelete('{{ route('categories.destroy', $category) }}',
                                        'Are you sure you want to delete <strong>{{ $category->name }}</strong>? Products in this category will be unassigned.' )"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-tags empty-icon"></i>
                                    <p class="empty-text">No categories yet.</p>
                                    @can('create')
                                        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm mt-2">
                                            <i class="bi bi-plus-lg me-1"></i> Create Category
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
