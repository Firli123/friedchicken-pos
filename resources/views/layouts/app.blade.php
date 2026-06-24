<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Fried Chicken POS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --red:#E53935; --red-dark:#C62828; --yellow:#FFC107;
            --bg:#F5F5F5; --sidebar:#1A1A2E; --border:#E0E0E0;
            --shadow:0 2px 12px rgba(0,0,0,0.08); --touch:48px;
        }
        * { font-family:'Plus Jakarta Sans',sans-serif; box-sizing:border-box; }
        body { background:var(--bg); margin:0; overflow-x:hidden; }

        /* Sidebar */
        #sidebar {
            width:240px; height:100vh; background:var(--sidebar);
            position:fixed; top:0; left:0; z-index:1000;
            display:flex; flex-direction:column;
            transition:transform 0.3s ease; overflow-y:auto;
        }
        #sidebar.hidden { transform:translateX(-100%); }

        .sb-brand {
            padding:18px 20px; border-bottom:1px solid rgba(255,255,255,0.08);
            display:flex; align-items:center; gap:10px;
        }
        .sb-icon {
            width:40px; height:40px; background:var(--red); border-radius:10px;
            display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0;
        }
        .sb-name { color:var(--yellow); font-size:0.92rem; font-weight:800; letter-spacing:0.5px; }
        .sb-sub { color:rgba(255,255,255,0.35); font-size:0.62rem; text-transform:uppercase; letter-spacing:1px; }

        .nav-label {
            padding:14px 20px 4px; font-size:0.6rem; text-transform:uppercase;
            letter-spacing:1.5px; color:rgba(255,255,255,0.3); font-weight:600;
        }
        .nav-link {
            display:flex; align-items:center; gap:10px; padding:0 20px;
            height:var(--touch); color:rgba(255,255,255,0.6); text-decoration:none;
            font-size:0.875rem; font-weight:500; border-left:3px solid transparent;
            transition:all 0.2s;
        }
        .nav-link:hover { background:rgba(255,255,255,0.06); color:#fff; }
        .nav-link.active { background:rgba(229,57,53,0.15); color:#fff; border-left-color:var(--red); }
        .nav-link i { font-size:1rem; width:20px; text-align:center; }

        .sb-footer {
            margin-top:auto; padding:16px 20px;
            border-top:1px solid rgba(255,255,255,0.08);
        }
        .sb-user { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
        .sb-avatar {
            width:36px; height:36px; background:var(--red); border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:0.85rem; font-weight:700; color:#fff; flex-shrink:0;
        }
        .sb-uname { color:#fff; font-size:0.82rem; font-weight:600; }
        .sb-urole { color:rgba(255,255,255,0.4); font-size:0.68rem; text-transform:capitalize; }
        .btn-logout {
            display:flex; align-items:center; justify-content:center; gap:6px;
            width:100%; height:40px; background:rgba(255,255,255,0.08);
            border:1px solid rgba(255,255,255,0.12); border-radius:8px;
            color:rgba(255,255,255,0.6); font-size:0.82rem;
            font-family:'Plus Jakarta Sans',sans-serif; cursor:pointer; transition:all 0.2s;
        }
        .btn-logout:hover { background:rgba(229,57,53,0.2); color:#fff; border-color:var(--red); }

        /* Overlay */
        #overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,0.5); z-index:999; backdrop-filter:blur(2px);
        }
        #overlay.show { display:block; }

        /* Main */
        #main { margin-left:240px; min-height:100vh; display:flex; flex-direction:column; transition:margin 0.3s; }
        #main.full { margin-left:0; }

        /* Topbar */
        #topbar {
            background:#fff; border-bottom:1px solid var(--border);
            height:60px; padding:0 20px;
            display:flex; align-items:center; justify-content:space-between;
            position:sticky; top:0; z-index:100;
            box-shadow:0 1px 4px rgba(0,0,0,0.05);
        }
        .tb-left { display:flex; align-items:center; gap:12px; }
        #menuBtn {
            width:44px; height:44px; border:none; background:#f5f5f5;
            border-radius:10px; display:flex; align-items:center; justify-content:center;
            font-size:1.2rem; cursor:pointer; transition:all 0.2s; color:#424242;
        }
        #menuBtn:hover { background:#ffe0e0; color:var(--red); }
        .page-title { font-size:1rem; font-weight:700; color:#1a1a1a; }
        #clock {
            font-size:0.82rem; color:#757575; background:#f5f5f5;
            padding:6px 12px; border-radius:8px; font-weight:600;
        }

        /* Content */
        #content { padding:20px; flex:1; }

        /* Components */
        .pos-card { background:#fff; border-radius:14px; box-shadow:var(--shadow); border:1px solid var(--border); }
        .stat-card { background:#fff; border-radius:14px; padding:20px; box-shadow:var(--shadow); border:1px solid var(--border); }
        .stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
        .table thead th {
            background:#fafafa; font-weight:700; font-size:0.75rem;
            text-transform:uppercase; letter-spacing:0.5px; color:#9e9e9e;
            border-bottom:2px solid var(--border); padding:12px 16px;
        }
        .table td { padding:14px 16px; vertical-align:middle; }
        .btn-primary-pos {
            background:var(--red); color:#fff; border:none; border-radius:10px;
            padding:0 20px; font-weight:700; font-size:0.88rem;
            min-height:var(--touch); display:inline-flex; align-items:center; gap:6px;
            cursor:pointer; transition:all 0.2s; font-family:'Plus Jakarta Sans',sans-serif;
        }
        .btn-primary-pos:hover { background:var(--red-dark); color:#fff; }
        .alert { border-radius:10px; border:none; }

        /* Tablet */
        @media (max-width:1024px) {
            #sidebar { transform:translateX(-100%); }
            #sidebar.show { transform:translateX(0); }
            #main { margin-left:0 !important; }
            #content { padding:14px; }
        }
        @media (max-width:768px) {
            .page-title { font-size:0.9rem; }
            #clock { display:none; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div id="overlay" onclick="closeSB()"></div>

<nav id="sidebar">
    <div class="sb-brand">
        <div class="sb-icon">🍗</div>
        <div>
            <div class="sb-name">FRIED CHICKEN</div>
            <div class="sb-sub">Point of Sale</div>
        </div>
    </div>

    <div class="mt-1">
        @if(auth()->user()->isOwner())
        <div class="nav-label">Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        @endif

        <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap-fill"></i> Kasir (POS)
        </a>
        <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Riwayat Transaksi
        </a>

        @if(auth()->user()->isOwner())
        <div class="nav-label">Manajemen</div>
        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Produk
        </a>
        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Pengguna
        </a>

        <div class="nav-label">Laporan</div>
        <a href="{{ route('reports.daily') }}" class="nav-link {{ request()->routeIs('reports.daily') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Laporan Harian
        </a>
        <a href="{{ route('reports.monthly') }}" class="nav-link {{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> Laporan Bulanan
        </a>

        <div class="nav-label">Sistem</div>
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Pengaturan
        </a>
        <a href="{{ route('backup.index') }}" class="nav-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
            <i class="bi bi-cloud-arrow-up"></i> Backup
        </a>
        @endif
    </div>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
            <div>
                <div class="sb-uname">{{ auth()->user()->name }}</div>
                <div class="sb-urole">{{ auth()->user()->role }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</nav>

<div id="main">
    <div id="topbar">
        <div class="tb-left">
            <button id="menuBtn" onclick="toggleSB()"><i class="bi bi-list"></i></button>
            <div class="page-title">@yield('page-title','Dashboard')</div>
        </div>
        <div id="clock"></div>
    </div>

    <div id="content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Clock
    setInterval(() => {
        const el = document.getElementById('clock');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
    }, 1000);

    // Sidebar
    function toggleSB() {
        const sb = document.getElementById('sidebar');
        const ov = document.getElementById('overlay');
        if (window.innerWidth <= 1024) {
            sb.classList.toggle('show');
            ov.classList.toggle('show');
        } else {
            sb.classList.toggle('hidden');
            document.getElementById('main').classList.toggle('full');
        }
    }

    function closeSB() {
        document.getElementById('sidebar').classList.remove('show');
        document.getElementById('overlay').classList.remove('show');
    }

    window.csrfToken = '{{ csrf_token() }}';
</script>
@stack('scripts')
</body>
</html>
