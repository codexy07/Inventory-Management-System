@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-icon', 'bi-person-gear')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-person-gear"></i>
                    Edit User: {{ $user->name }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3" style="background:#f8fafc;">
                        <div class="user-avatar" style="width:48px;height:48px;font-size:1.1rem;border-radius:14px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <div class="text-muted small">{{ $user->email }}{{ $user->username ? ' &middot; @' . $user->username : '' }}</div>
                            <span class="role-badge {{ $user->role }} mt-1">
                                {{ $user->role === 'admin' ? 'Admin' : 'Staff' }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select id="role" name="role"
                                class="form-select @error('role') is-invalid @enderror" required>
                                <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label">Username
                                <small class="text-muted fw-normal">(required for admins)</small>
                            </label>
                            <input type="text" id="username" name="username"
                                class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username', $user->username) }}"
                                placeholder="johndoe"
                                {{ old('role', $user->role) === 'admin' ? 'required' : '' }}>
                            <div class="form-text small">Letters, numbers, dashes and underscores only.</div>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Update User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
