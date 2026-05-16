<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operational Dashboard - Embun Cafe</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; background-color: #f8f9f6; color: #333; }
        .sidebar { width: 250px; background-color: #f4f3ed; padding: 20px; height: 100vh; border-right: 1px solid #ddd; display: flex; flex-direction: column; box-sizing: border-box; }
        .nav-link { display: flex; align-items: center; gap: 10px; padding: 12px 15px; text-decoration: none; color: #555; border-radius: 8px; margin-bottom: 5px; font-weight: 500; }
        .nav-link.active { background-color: #e2e8e4; color: #2e5a40; font-weight: bold; }
        .nav-link:hover:not(.active) { background-color: #eae9e4; }
        
        .main-content { flex: 1; padding: 30px; overflow-y: auto; height: 100vh; box-sizing: border-box; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        
        /* Utility */
        .text-green { color: #2e5a40; } .text-red { color: #dc3545; }
        .badge { padding: 4px 10px; border-radius: 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-warning { background: #fdf5d3; color: #856404; }
        .badge-success { background: #e6f4ea; color: #1e8e3e; }
        .btn-primary { background: #4c7c5f; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; }
        .btn-outline { background: transparent; color: #4c7c5f; border: 1px solid #4c7c5f; padding: 8px 15px; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div style="margin-bottom: 30px;">
            <h3 style="color: #4c7c5f; margin: 0;">☕ Embun Cafe</h3>
            <p style="font-size: 10px; color: #888; margin: 2px 0 0 0; letter-spacing: 1px;">OPERATIONAL DASHBOARD</p>
        </div>
        
        <div style="flex: 1;">
            <a href="/karyawan/dashboard" class="nav-link {{ request()->is('karyawan/dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="/karyawan/stok" class="nav-link {{ request()->is('karyawan/stok') ? 'active' : '' }}">Data Stok Bahan</a>
            <a href="/karyawan/reservasi" class="nav-link {{ request()->is('karyawan/reservasi') ? 'active' : '' }}">Verifikasi Reservasi</a>
            <a href="/karyawan/online" class="nav-link {{ request()->is('karyawan/online') ? 'active' : '' }}">Pesanan Online Masuk</a>
            <a href="/karyawan/offline" class="nav-link {{ request()->is('karyawan/Offline') ? 'active' : '' }}">Pesanan Offline Masuk</a>
            <a href="#" class="nav-link">Pengiriman Stok Bahan</a>
        </div>
        
        <div style="border-top: 1px solid #ddd; padding-top: 15px;">
            <a href="#" class="nav-link">⚙️ Settings</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #dc3545; text-decoration: none; font-weight: bold; display: flex; align-items: center; gap: 10px; padding: 10px;">
    🚪 Logout
</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h3 style="margin: 0; color: #333;">@yield('title', 'Embun Cafe Operations')</h3>
            <div style="display: flex; align-items: center; gap: 20px;">
                <input type="text" placeholder="🔍 Search..." style="padding: 8px 15px; border-radius: 20px; border: 1px solid #ddd; background: #f9f9f9;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="text-align: right;">
                        <strong style="font-size: 14px; display: block;">Budi Santoso</strong>
                        <span style="font-size: 10px; color: #888;">KITCHEN MANAGER</span>
                    </div>
                    <div style="width: 35px; height: 35px; background: #4c7c5f; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center;">BS</div>
                </div>
            </div>
        </div>
        @yield('content')
    </div>

    <script>const API_URL = 'http://127.0.0.1:8000/api';</script>
    @yield('scripts')
</body>
</html>
