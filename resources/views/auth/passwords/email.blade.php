@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="text-center mb-4">
    <h4 class="fw-bold text-white mb-1">Forgot Password?</h4>
    <p class="text-white-50 small">Enter your email and we'll send you a reset link</p>
</div>

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-4">
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

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
        <i class="bi bi-send me-1"></i> Send Reset Link
    </button>
</form>

<div class="text-center mt-4">
    <a href="{{ route('login') }}" class="text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i> Back to Login
    </a>
</div>
@endsection
