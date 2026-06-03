<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        {{-- ===== SIDEBAR ===== --}}
        <div class="sidebar" id="sidebar">
            {{-- Brand --}}
            <a href="{{ route('dashboard') }}" class="sidebar-brand d-flex align-items-center text-white text-decoration-none">
                <div class="brand-icon">
                    <i class="bi bi-box-seam fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1rem;">InventoryMS</div>
                    <div class="small" style="font-size:0.65rem;color:rgba(255,255,255,0.35);">Management System</div>
                </div>
            </a>

            <hr class="sidebar-divider">

            <div class="sidebar-nav-scroll">
                <div class="sidebar-label">Navigation</div>
                <ul class="nav nav-pills flex-column mb-0">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="bi bi-box"></i>
                            Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="bi bi-cart"></i>
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            <i class="bi bi-truck"></i>
                            Suppliers
                        </a>
                    </li>
                </ul>

                @can('create')
                <div class="sidebar-label">Operations</div>
                <ul class="nav nav-pills flex-column mb-0">
                    <li>
                        <a href="{{ route('stock.in.index') }}" class="nav-link {{ request()->routeIs('stock.in*') ? 'active' : '' }}">
                            <i class="bi bi-arrow-down-circle"></i>
                            Stock In
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('stock.out.index') }}" class="nav-link {{ request()->routeIs('stock.out*') ? 'active' : '' }}">
                            <i class="bi bi-arrow-up-circle"></i>
                            Stock Out
                        </a>
                    </li>
                </ul>
                @endcan

                <div class="sidebar-label">Reports</div>
                <ul class="nav nav-pills flex-column mb-0">
                    <li>
                        <a href="{{ route('reports.inventory') }}" class="nav-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text"></i>
                            Inventory Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.sales') }}" class="nav-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                            <i class="bi bi-currency-dollar"></i>
                            Sales Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.products') }}" class="nav-link {{ request()->routeIs('reports.products') ? 'active' : '' }}">
                            <i class="bi bi-box"></i>
                            Product Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.history') }}" class="nav-link {{ request()->routeIs('reports.history') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i>
                            Stock History
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.lowstock') }}" class="nav-link {{ request()->routeIs('reports.lowstock') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-triangle"></i>
                            Low Stock Alert
                        </a>
                    </li>
                </ul>

                @can('admin')
                <div class="sidebar-label">Administration</div>
                <ul class="nav nav-pills flex-column mb-0">
                    <li>
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tags"></i>
                            Categories
                            <span class="badge-nav">{{ \App\Models\Category::count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i>
                            Users
                            <span class="badge-nav">{{ \App\Models\User::count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i>
                            Activity Logs
                        </a>
                    </li>
                </ul>
                @endcan
            </div>

            {{-- Sidebar Footer --}}
            <div class="sidebar-footer">
                <div class="user-dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-white small text-truncate">{{ Auth::user()->name }}</div>
                        <span class="role-badge {{ Auth::user()->role }}">
                            {{ Auth::user()->isAdmin() ? 'Admin' : 'Staff' }}
                        </span>
                    </div>
                    <i class="bi bi-chevron-up text-white-50" style="font-size:0.7rem;"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-dark w-100 mt-1" style="--bs-dropdown-min-width:240px;">
                    <li>
                        <div class="dropdown-header">{{ Auth::user()->email }}</div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="bi bi-person-gear me-2"></i>My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item w-100">
                                <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="main-content">
            {{-- Top Navbar --}}
            <nav class="top-navbar navbar px-4">
                <div class="container-fluid">
                    <div class="page-title-area">
                        <button class="btn btn-sm px-2 d-md-none" style="color:#64748b;" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" onclick="document.getElementById('sidebar').classList.toggle('show')">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <div class="title-icon">
                            <i class="bi @yield('page-icon', 'bi-speedometer2')"></i>
                        </div>
                        <span class="fw-bold fs-5" style="color:#1e293b;letter-spacing:-0.3px;">
                            @yield('page-title', 'Dashboard')
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted small">{{ now()->format('l, F j, Y') }}</span>
                    </div>
                </div>
            </nav>

            {{-- Flash Messages --}}
            <div class="px-4 pt-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="bi bi-x-circle-fill me-2 fs-5"></i>
                        <span>{{ session('error') }}</span>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any() && !session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                        <span>Please check the form for errors.</span>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            {{-- Page Content --}}
            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="deleteModalBody">
                    Are you sure you want to delete this item? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Flatpickr
            flatpickr('.datepicker', { dateFormat: 'Y-m-d', allowInput: true });

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function(e) {
                const sidebar = document.getElementById('sidebar');
                const toggle = document.querySelector('[data-bs-toggle="collapse"][data-bs-target="#sidebar"]');
                if (window.innerWidth <= 768 && sidebar.classList.contains('show') &&
                    !sidebar.contains(e.target) && toggle && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            });
        });

        function confirmDelete(actionUrl, customMessage) {
            document.getElementById('deleteForm').action = actionUrl;
            if (customMessage) {
                document.getElementById('deleteModalBody').innerHTML = customMessage;
            } else {
                document.getElementById('deleteModalBody').innerHTML = 'Are you sure you want to delete this item? This action cannot be undone.';
            }
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
    @stack('scripts')
</body>
</html>
