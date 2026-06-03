<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0b0d14;
            background-image:
                radial-gradient(ellipse 80% 60% at 50% 0%, rgba(217, 119, 6, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 20% 100%, rgba(59, 130, 246, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 80% 100%, rgba(217, 119, 6, 0.05) 0%, transparent 50%);
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                repeating-linear-gradient(0deg, transparent, transparent 80px, rgba(255,255,255,0.008) 80px, rgba(255,255,255,0.008) 81px),
                repeating-linear-gradient(90deg, transparent, transparent 80px, rgba(255,255,255,0.008) 80px, rgba(255,255,255,0.008) 81px);
            pointer-events: none;
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand .logo-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #d97706, #f59e0b);
            box-shadow: 0 8px 32px rgba(217, 119, 6, 0.35);
            position: relative;
        }

        .brand .logo-icon::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(217,119,6,0.3), rgba(245,158,11,0.1));
            z-index: -1;
            filter: blur(8px);
        }

        .brand .logo-icon i {
            font-size: 28px;
            color: #fff;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 28px;
            color: #fff;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .brand .subtitle {
            color: rgba(255,255,255,0.35);
            font-size: 13px;
            font-weight: 400;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .brand .subtitle span {
            color: #fbbf24;
        }

        .login-card {
            background: rgba(18, 20, 30, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 36px 32px 32px;
            box-shadow:
                0 4px 24px rgba(0,0,0,0.4),
                inset 0 1px 0 rgba(255,255,255,0.04);
        }

        .login-card .card-header-text {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-card .card-header-text h2 {
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .login-card .card-header-text p {
            color: rgba(255,255,255,0.35);
            font-size: 13px;
        }

        .form-label {
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .login-card .input-group {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .login-card .input-group:focus-within {
            border-color: rgba(245, 158, 11, 0.5);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .login-card .input-group-text {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.25);
            padding-left: 14px;
            padding-right: 8px;
        }

        .login-card .form-control {
            background: transparent;
            border: none;
            color: #fff;
            padding: 12px 14px 12px 4px;
            font-size: 14px;
            box-shadow: none;
        }

        .login-card .form-control::placeholder {
            color: rgba(255,255,255,0.2);
        }

        .login-card .form-control:focus {
            box-shadow: none;
        }

        .login-card .form-control.is-invalid {
            background: transparent;
        }

        .login-card .invalid-feedback {
            font-size: 12px;
            color: #f87171;
            margin-top: 4px;
            padding-left: 4px;
        }

        .login-card .form-check-label {
            color: rgba(255,255,255,0.5);
            font-size: 13px;
        }

        .login-card .form-check-input {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
        }

        .login-card .form-check-input:checked {
            background: #d97706;
            border-color: #d97706;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            color: #fff;
            background: linear-gradient(135deg, #d97706, #f59e0b);
            box-shadow: 0 4px 20px rgba(217, 119, 6, 0.3);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 28px rgba(217, 119, 6, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-card .forgot-link {
            color: rgba(255,255,255,0.35);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-card .forgot-link:hover {
            color: #fbbf24;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
        }

        .login-footer a {
            color: rgba(255,255,255,0.35);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-footer a:hover {
            color: #fbbf24;
        }

        /* Flash messages */
        .login-card .alert {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .login-card .alert-success {
            background: rgba(5, 150, 105, 0.1);
            border: 1px solid rgba(5, 150, 105, 0.2);
            color: #6ee7b7;
        }

        .login-card .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.2);
            color: #fca5a5;
        }

        .login-card .alert .btn-close {
            filter: invert(1) brightness(0.5);
        }

        .error-box {
            background: rgba(220, 38, 38, 0.08);
            border: 1px solid rgba(220, 38, 38, 0.15);
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 20px;
            color: #fca5a5;
            font-size: 13px;
        }

        .separator {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: rgba(255,255,255,0.15);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.06);
        }
    </style>
</head>
<body>
    <div class="login-container">
        {{-- Brand --}}
        <div class="brand">
            <div class="logo-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <h1>InventoryMS</h1>
            <div class="subtitle"><span>Inventory</span> Management System</div>
        </div>

        {{-- Login Card --}}
        <div class="login-card">
            <div class="card-header-text">
                <h2>Welcome Back</h2>
                <p>Sign in to manage your inventory</p>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="login" class="form-label">Email or Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" id="login" name="login"
                            class="form-control @error('login') is-invalid @enderror"
                            value="{{ old('login') }}" placeholder="you@example.com or username" required autofocus autocomplete="username">
                        @error('login')
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
                            placeholder="Enter your password" required autocomplete="current-password">
                        <button class="input-group-text toggle-password" type="button" data-target="password" style="cursor:pointer;background:transparent;border:none;color:rgba(255,255,255,0.25);">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Keep me signed in</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </button>
            </form>

            <div class="separator">or</div>

            <div class="text-center">
                <p class="mb-0">
                    <span style="color:rgba(255,255,255,0.35);font-size:13px;">Don't have an account?</span>
                    <a href="{{ route('register') }}" class="text-decoration-none fw-semibold" style="color:#fbbf24;font-size:13px;">Register</a>
                </p>
            </div>
        </div>

        <div class="login-footer">
            <a href="/">&copy; {{ date('Y') }} InventoryMS</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
