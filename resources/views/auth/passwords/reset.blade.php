@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="text-center mb-4">
    <h4 class="fw-bold text-white mb-1">Reset Password</h4>
    <p class="text-white-50 small">Enter your new password</p>
</div>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" id="email" name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
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
        <label for="password_confirmation" class="form-label">Confirm New Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" id="password_confirmation" name="password_confirmation"
                class="form-control" placeholder="Repeat password" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
        <i class="bi bi-check-circle me-1"></i> Reset Password
    </button>
</form>
@endsection
