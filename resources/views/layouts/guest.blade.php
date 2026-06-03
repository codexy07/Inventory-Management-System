<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="auth-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6">
                    {{-- Brand --}}
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3"
                            style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#d97706,#f59e0b);box-shadow:0 4px 20px rgba(217,119,6,0.3);">
                            <i class="bi bi-box-seam fs-3 text-white"></i>
                        </div>
                        <h3 class="fw-bold text-white mb-1" style="letter-spacing:-0.5px;">InventoryMS</h3>
                        <p class="text-white-50 small">Inventory Management System</p>
                    </div>

                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert" style="background:rgba(5,150,105,0.15);border:1px solid rgba(5,150,105,0.2);color:#6ee7b7;">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <span>{{ session('success') }}</span>
                            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3" role="alert" style="background:rgba(220,38,38,0.15);border:1px solid rgba(220,38,38,0.2);color:#fca5a5;">
                            <i class="bi bi-x-circle-fill me-2 fs-5"></i>
                            <span>{{ session('error') }}</span>
                            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Auth Card --}}
                    <div class="auth-card p-4 p-lg-5">
                        @yield('content')
                    </div>

                    {{-- Footer --}}
                    <div class="text-center mt-4">
                        <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} InventoryMS. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
