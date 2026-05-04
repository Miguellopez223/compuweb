<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COMPUWEB SYSTEM</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f7f9;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
            background: #f4f7f9;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #31798a;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar-header {
            padding: 30px 25px;
        }

        .sidebar-header h1 {
            margin: 0;
            font-size: 25px;
            letter-spacing: 4px;
            line-height: 1.2;
            font-weight: 900;
        }

        .sidebar-header p {
            font-size: 12px;
            font-weight: bold;
            opacity: 0.85;
            margin-top: 12px;
        }

        .sidebar-menu a {
            display: block;
            padding: 18px 25px;
            color: #e8f6f8;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            border-top: 1px dashed rgba(255,255,255,0.25);
        }

        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.13);
        }

        .sidebar-bottom {
            padding: 25px;
        }

        .sale-button {
            display: block;
            width: 100%;
            background: #008a3d;
            color: white;
            text-align: center;
            padding: 14px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 800;
        }

        .sale-button:hover {
            background: #007235;
        }

        /* MAIN */
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
            background: #f4f7f9;
        }

        .topbar {
            height: 65px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
        }

        .topbar-title {
            font-size: 17px;
            font-weight: 900;
            color: #31798a;
            letter-spacing: 1px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-box {
            width: 320px;
            border: 1px solid #d5dde2;
            background: #f6f8fa;
            padding: 12px 16px;
            border-radius: 10px;
            color: #1f2937;
        }

        .logout-button {
            background: none;
            border: none;
            color: #1f2937;
            font-weight: 800;
            cursor: pointer;
            font-size: 15px;
        }

        .logout-button:hover {
            color: #dc2626;
        }

        .page-content {
            padding: 35px;
        }

        .page-title {
            font-size: 30px;
            font-weight: 900;
            margin: 0 0 6px 0;
            color: #1f2937;
        }

        .page-subtitle {
            margin: 0 0 28px 0;
            color: #64748b;
            font-size: 15px;
        }

        /* CARD GENERAL */
        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        /* DASHBOARD */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border: 1px solid #dce3e8;
            border-top: 5px solid #31798a;
            border-radius: 5px;
            text-align: center;
            padding: 28px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .stat-card.alert {
            background: #ffd7d5;
            color: #b91c1c;
            border-top-color: #ff9f9b;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #111827;
        }

        .stat-card.alert .stat-number {
            color: #b91c1c;
        }

        .stat-label {
            margin-top: 8px;
            font-size: 13px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
        }

        /* FORMS */
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
            font-size: 13px;
            color: #475569;
            font-weight: 900;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .form-control {
            width: 100%;
            border: 1px solid #cfd8de;
            background: #f8fafc;
            padding: 15px;
            border-radius: 3px;
            font-size: 15px;
            color: #1f2937;
        }

        .form-control:focus {
            outline: none;
            border-color: #31798a;
            box-shadow: 0 0 0 2px rgba(49, 121, 138, 0.15);
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 28px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }

        /* BUTTONS */
        .btn {
            display: inline-block;
            padding: 13px 22px;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-weight: 900;
            font-size: 15px;
        }

        .btn-primary {
            background: #008a3d;
            color: white;
        }

        .btn-primary:hover {
            background: #007235;
        }

        .btn-secondary {
            background: white;
            color: #31798a;
            border: 2px solid #9cb7c0;
        }

        .btn-secondary:hover {
            background: #eef6f8;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        /* TABLES */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f1f5f9;
            padding: 14px;
            text-align: left;
            font-size: 13px;
            color: #475569;
            text-transform: uppercase;
        }

        .table td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            color: #334155;
        }

        .table tr:hover {
            background: #f8fafc;
        }

        /* BADGES */
        .badge {
            padding: 7px 13px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 900;
            display: inline-block;
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

        /* RESPONSIVE SIMPLE */
        @media (max-width: 900px) {
            .sidebar {
                width: 210px;
            }

            .main-content {
                margin-left: 210px;
                width: calc(100% - 210px);
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .search-box {
                width: 220px;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        <aside class="sidebar">
            <div>
                <div class="sidebar-header">
                    <h1>COMPUWEB<br>SYSTEM</h1>
                    <p>ENTERPRISE MANAGEMENT</p>
                </div>

                <nav class="sidebar-menu">
                    <a href="{{ route('dashboard') }}">▦ Dashboard</a>
                    <a href="{{ route('productos.create') }}">✚ New Product</a>
                    <a href="{{ route('movimientos.index') }}">↔ Movements</a>
                    <a href="{{ route('productos.index') }}">▣ Inventory</a>
                    <a href="#">▥ Reports</a>
                    <a href="#">▱ Conversational Commerce</a>
                    <a href="{{ route('categorias.index') }}">⌕ Categories</a>
                    <a href="{{ route('profile.edit') }}">⚙ Configuration</a>
                </nav>
            </div>

            <div class="sidebar-bottom">
                <a href="#" class="sale-button">Register Sale</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-title">COMPUWEB SYSTEM</div>

                <div class="topbar-right">
                    <input type="text" class="search-box" placeholder="Search...">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-button">Logout</button>
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