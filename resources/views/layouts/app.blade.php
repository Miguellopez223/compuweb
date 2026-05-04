<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COMPUWEB SYSTEM</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            background: #f4f7f9;
            font-family: Arial, Helvetica, sans-serif;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #2f7484;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
        }

        .sidebar-header {
            padding: 28px 24px;
        }

        .sidebar-header h1 {
            font-size: 22px;
            letter-spacing: 3px;
            font-weight: 800;
            line-height: 1.2;
        }

        .sidebar-header span {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            font-weight: bold;
            opacity: 0.8;
        }

        .sidebar-menu {
            margin-top: 10px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #dceff3;
            text-decoration: none;
            padding: 18px 24px;
            border-top: 1px dashed rgba(255, 255, 255, 0.25);
            font-weight: 700;
            font-size: 15px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.13);
            color: white;
        }

        .sidebar-bottom {
            padding: 22px;
        }

        .sale-button {
            display: block;
            width: 100%;
            background: #008a3d;
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 5px;
            font-weight: 800;
            text-decoration: none;
        }

        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        .topbar {
            height: 58px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }

        .topbar-title {
            font-weight: 800;
            color: #2f7484;
            letter-spacing: 1px;
        }

        .search-box {
            width: 300px;
            background: #f3f6f8;
            border: 1px solid #d9e1e5;
            border-radius: 10px;
            padding: 10px 14px;
            color: #6b7280;
        }

        .page-content {
            padding: 35px;
        }

        .page-title {
            font-size: 30px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 25px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border: 1px solid #dce3e8;
            border-top: 5px solid #2f7484;
            border-radius: 4px;
            text-align: center;
            padding: 25px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        .stat-card.alert {
            background: #ffd7d5;
            color: #b91c1c;
            border-top-color: #ffb0ad;
        }

        .stat-number {
            font-size: 34px;
            font-weight: 900;
            color: #111827;
        }

        .stat-card.alert .stat-number {
            color: #b91c1c;
        }

        .stat-label {
            margin-top: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #6b7280;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .table th {
            background: #f0f3f5;
            padding: 15px;
            font-size: 13px;
            color: #536471;
            text-align: left;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #edf0f2;
            color: #374151;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            border: none;
            padding: 12px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 800;
            cursor: pointer;
        }

        .btn-primary {
            background: #008a3d;
            color: white;
        }

        .btn-secondary {
            background: white;
            color: #2f7484;
            border: 2px solid #9cb7c0;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #536471;
        }

        .form-control {
            width: 100%;
            border: 1px solid #cfd8de;
            background: #f8fafb;
            padding: 15px;
            border-radius: 2px;
            font-size: 15px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 14px;
            margin-top: 28px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }

        .badge {
            padding: 7px 13px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-red {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-yellow {
            background: #fef3c7;
            color: #a16207;
        }

        .user-box {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 13px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: #0f9f6e;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
        }
    </style>
</head>

<body>
    <div class="app-container">

        <aside class="sidebar">
            <div>
                <div class="sidebar-header">
                    <h1>COMPUWEB<br>SYSTEM</h1>
                    <span>ENTERPRISE MANAGEMENT</span>
                </div>

                <nav class="sidebar-menu">
                    <a href="{{ route('dashboard') }}">▦ Dashboard</a>
                    <a href="{{ route('productos.create') }}">✚ New Product</a>
                    <a href="#">↔ Movements</a>
                    <a href="{{ route('productos.index') }}">▣ Inventory</a>
                    <a href="#">▥ Reports</a>
                    <a href="#">▱ Conversational Commerce</a>
                    <a href="{{ route('categorias.index') }}">⌕ Categories</a>
                    <a href="{{ route('profile.edit') }}">⚙ Configuration</a>
                </nav>
            </div>

            <div class="sidebar-bottom">
                <a href="#" class="sale-button">Register Sale</a>

                <div style="margin-top: 25px;" class="user-box">
                    <div class="user-avatar">US</div>
                    <div>
                        <strong>{{ Auth::user()->name ?? 'Admin User' }}</strong><br>
                        <small>{{ Auth::user()->email ?? 'admin@compuweb.com' }}</small>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-title">COMPUWEB SYSTEM</div>

                <div style="display:flex; align-items:center; gap:20px;">
                    <input class="search-box" type="text" placeholder="Search...">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button style="border:none; background:none; font-weight:700; cursor:pointer;">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <section class="page-content">
                {{ $slot }}
            </section>
        </main>

    </div>
</body>
</html>