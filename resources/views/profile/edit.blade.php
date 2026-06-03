@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-icon', 'bi-person')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        {{-- Profile Card --}}
        <div class="card">
            <div class="card-body p-4 text-center">
                <div class="user-avatar mx-auto mb-3" style="width:80px;height:80px;font-size:2rem;border-radius:20px;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <span class="role-badge {{ $user->role }} mb-2">{{ $user->isAdmin() ? 'Admin' : 'Staff' }}</span>
                <p class="text-muted small mt-2">{{ $user->email }}</p>
                <div class="text-muted small">
                    <i class="bi bi-calendar me-1"></i> Joined {{ $user->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        {{-- Edit Profile --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-person-gear"></i> Edit Profile
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username"
                                class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username', $user->username) }}">
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text small">Admins use this for login.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-lock"></i> Change Password
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <input type="password" id="current_password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-shield-lock me-1"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
