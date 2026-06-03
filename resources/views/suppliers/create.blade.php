@extends('layouts.admin')

@section('title', 'Create Supplier')
@section('page-title', 'Create Supplier')
@section('page-icon', 'bi-plus-circle')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-plus-circle"></i>
                    New Supplier Details
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('suppliers.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Enter supplier name" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}" placeholder="+1 (555) 000-0000" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" placeholder="supplier@example.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" name="address"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address') }}" placeholder="Full address">
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Create Supplier
                        </button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
