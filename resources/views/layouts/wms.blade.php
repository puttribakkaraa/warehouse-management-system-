<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WMS') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <style>
            :root {
                --primary-color: #4f46e5;
                --primary-hover: #4338ca;
                --secondary-color: #6366f1;
                --success-color: #10b981;
                --warning-color: #f59e0b;
                --danger-color: #ef4444;
                --dark-color: #1f2937;
                --light-color: #f3f4f6;
            }  
            
            body {
                font-family: 'Figtree', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }
            
            .sidebar {
                background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
                min-height: 100vh;
                width: 260px;
                position: fixed;
                left: 0;
                top: 0;
                z-index: 100;
                box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
                overflow-y: auto;
                padding-bottom: 2rem;
            }
            
            .sidebar-brand {
                padding: 1.5rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .sidebar-brand h2 {
                color: #fff;
                font-weight: 700;
                font-size: 1.5rem;
                margin: 0;
            }
            
            .sidebar-brand span {
                color: #a5b4fc;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.1em;
            }
            
            .sidebar-menu {
                padding: 1rem 0;
            }
            
            .sidebar-menu .menu-header {
                color: #a5b4fc;
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                padding: 1rem 1.5rem 0.5rem;
                font-weight: 600;
            }
            
            .sidebar-menu a {
                display: flex;
                align-items: center;
                padding: 0.75rem 1.5rem;
                color: #c7d2fe;
                text-decoration: none;
                transition: all 0.3s ease;
                border-left: 3px solid transparent;
            }
            
            .sidebar-menu a:hover {
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
                border-left-color: #818cf8;
            }
            
            .sidebar-menu a.active {
                background: rgba(99, 102, 241, 0.3);
                color: #fff;
                border-left-color: #818cf8;
            }
            
            .sidebar-menu a i {
                margin-right: 0.75rem;
                font-size: 1.1rem;
                width: 24px;
                text-align: center;
            }
            
            .main-content {
                margin-left: 260px;
                padding: 2rem;
            }
            
            .top-bar {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 1rem;
                padding: 1rem 1.5rem;
                margin-bottom: 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            
            .page-title {
                color: var(--dark-color);
                font-weight: 700;
                font-size: 1.5rem;
                margin: 0;
            }
            
            .card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 1rem;
                border: none;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                margin-bottom: 1.5rem;
            }
            
            .card-header {
                background: transparent;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                padding: 1.25rem 1.5rem;
                font-weight: 600;
                color: var(--dark-color);
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .stat-card {
                border-radius: 1rem;
                padding: 1.5rem;
                color: #fff;
                position: relative;
                overflow: hidden;
            }
            
            .stat-card::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -50%;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
            }
            
            .stat-card.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
            .stat-card.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
            .stat-card.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
            .stat-card.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
            .stat-card.info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
            
            .stat-card .stat-icon {
                font-size: 2.5rem;
                opacity: 0.3;
                position: absolute;
                right: 1rem;
                top: 1rem;
            }
            
            .stat-card .stat-value {
                font-size: 2rem;
                font-weight: 700;
                margin-bottom: 0.25rem;
            }
            
            .stat-card .stat-label {
                font-size: 0.875rem;
                opacity: 0.9;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                padding: 0.625rem 1.25rem;
                border-radius: 0.5rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
                background: linear-gradient(135deg, #5a4fd4 0%, #6a4199 100%);
            }
            
            .btn-success {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                border: none;
            }
            
            .btn-danger {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                border: none;
            }
            
            .btn-warning {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                border: none;
                color: #fff;
            }
            
            .btn-warning:hover {
                color: #fff;
            }
            
            .table {
                margin-bottom: 0;
            }
            
            .table th {
                background: rgba(99, 102, 241, 0.1);
                color: var(--dark-color);
                font-weight: 600;
                border: none;
                padding: 1rem;
            }
            
            .table td {
                padding: 1rem;
                vertical-align: middle;
                border-color: rgba(0, 0, 0, 0.05);
            }
            
            .badge {
                padding: 0.5rem 0.75rem;
                border-radius: 0.5rem;
                font-weight: 500;
            }
            
            .badge-success { background: rgba(16, 185, 129, 0.1); color: #059669; }
            .badge-warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
            .badge-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
            .badge-info { background: rgba(6, 182, 212, 0.1); color: #0891b2; }
            
            .form-control, .form-select {
                border-radius: 0.5rem;
                border: 1px solid rgba(0, 0, 0, 0.1);
                padding: 0.625rem 1rem;
            }
            
            .form-control:focus, .form-select:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            }
            
            .alert {
                border-radius: 0.75rem;
                border: none;
                padding: 1rem 1.25rem;
                font-weight: 500;
            }
            
            .alert-success {
                background: #d1fae5;
                color: #065f46;
                border-left: 4px solid #10b981;
            }
            
            .alert-danger {
                background: #fee2e2;
                color: #991b1b;
                border-left: 4px solid #ef4444;
            }
            
            .alert-info {
                background: #dbeafe;
                color: #1e40af;
                border-left: 4px solid #3b82f6;
            }
            
            .alert-warning {
                background: #fef3c7;
                color: #92400e;
                border-left: 4px solid #f59e0b;
            }
            
            .low-stock-alert {
                background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                border-radius: 0.75rem;
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .user-dropdown {
                background: transparent;
                border: 1px solid rgba(0, 0, 0, 0.1);
                border-radius: 0.5rem;
                padding: 0.5rem 1rem;
                color: var(--dark-color);
            }
            
            .pagination .page-link {
                border-radius: 0.5rem;
                margin: 0 0.125rem;
                border: none;
                color: var(--primary-color);
            }
            
            .pagination .page-item.active .page-link {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            @media (max-width: 768px) {
                .sidebar {
                    transform: translateX(-100%);
                }
                .main-content {
                    margin-left: 0;
                }
            }
        </style>
        @stack('styles')
    </head>
    <body>
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <h2><i class="bi bi-box-seam"></i> WMS</h2>
                <span>Warehouse Management</span>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-header">Main Menu</div>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                
                <div class="menu-header">Master Data</div>
                <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'active' : '' }}">
                    <i class="bi bi-box"></i> Stok Barang
                </a>
                <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i> Supplier
                </a>
                <a href="{{ route('menus.index') }}" class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> Menu / Resep
                </a>
                
                <div class="menu-header">Transaksi</div>
                <a href="{{ route('incoming-goods.index') }}" class="{{ request()->routeIs('incoming-goods.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-down-circle"></i> Barang Masuk
                </a>
                <a href="{{ route('outgoing-goods.index') }}" class="{{ request()->routeIs('outgoing-goods.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-up-circle"></i> Barang Keluar
                </a>
                <a href="{{ route('supplier-bills.index') }}" class="{{ request()->routeIs('supplier-bills.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Tagihan Supplier
                </a>
                
                <div class="menu-header">Laporan</div>
                <a href="{{ route('reports.weekly') }}" class="{{ request()->routeIs('reports.weekly') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week"></i> Laporan Mingguan
                </a>
                <a href="{{ route('reports.monthly') }}" class="{{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
                    <i class="bi bi-calendar-month"></i> Laporan Bulanan
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="user-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
    </body>
</html>
