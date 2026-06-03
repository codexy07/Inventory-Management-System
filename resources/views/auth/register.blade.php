@extends('layouts.guest')

@section('title', 'Create Account')

@section('content')
<div class="text-center mb-4">
    <h4 class="fw-bold text-white mb-1">Create Account</h4>
    <p class="text-white-50 small">Join the inventory management system</p>
</div>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" id="name" name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}" placeholder="John Doe" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" id="email" name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="you@example.com" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" id="password" name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Min. 8 characters" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" id="password_confirmation" name="password_confirmation"
                class="form-control" placeholder="Repeat your password" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
        <i class="bi bi-person-check me-1"></i> Create Account
    </button>
</form>

<div class="text-center mt-4">
    <p class="mb-0 small">
        <span class="text-white-50">Already have an account?</span>
        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Sign In</a>
    </p>
</div>
@endsection
