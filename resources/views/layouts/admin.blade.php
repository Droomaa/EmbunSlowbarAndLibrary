<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Embun Cafe</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; background-color: #f8f9f6; color: #333; }
        .sidebar { width: 240px; background-color: #f1efea; padding: 20px; height: 100vh; border-right: 1px solid #ddd; display: flex; flex-direction: column; box-sizing: border-box; }
        .nav-link { display: flex; align-items: center; padding: 12px 15px; text-decoration: none; color: #555; border-radius: 8px; margin-bottom: 8px; font-weight: 500; transition: 0.2s; }
        .nav-link.active { background-color: #4c7c5f; color: white; }
        .nav-link:hover:not(.active) { background-color: #e2e8e4; }
        
        .main-content { flex: 1; padding: 30px; overflow-y: auto; height: 100vh; box-sizing: border-box; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px; }
        
        /* Utility */
        .text-green { color: #2e5a40; } .text-red { color: #dc3545; } .text-yellow { color: #d39e00; }
        .badge { padding: 4px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #e6f4ea; color: #1e8e3e; }
        .badge-warning { background: #fdf5d3; color: #856404; }
        .badge-danger { background: #fce8e6; color: #d93025; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px 10px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        th { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Profile Block */
        .profile-block { display: flex; align-items: center; gap: 10px; padding: 10px; background: #e8e6df; border-radius: 8px; margin-bottom: 20px; }
        .profile-avatar { width: 40px; height: 40px; background: #2e5a40; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        
        /* Search Bar Style */
        .global-search { width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 13px; background: white; box-sizing: border-box; transition: 0.2s; }
        .global-search:focus { border-color: #4c7c5f; box-shadow: 0 0 0 3px rgba(76,124,95,0.15); }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2 style="color: #2e5a40; margin-top: 0;">Embun Cafe</h2>
        
        @if(!request()->is('admin/transaksi'))
        <div class="profile-block">
            <div class="profile-avatar">AD</div>
            <div style="line-height: 1.2;">
                <strong style="font-size: 14px; display: block;">Admin</strong>
                <span style="font-size: 11px; color: #666;">Embun Cafe Management</span>
            </div>
        </div>
        @endif

        <div style="flex: 1;">
            <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="/admin/penjualan" class="nav-link {{ request()->is('admin/penjualan') ? 'active' : '' }}">Laporan Penjualan</a>
            <a href="/admin/stok" class="nav-link {{ request()->is('admin/stok') ? 'active' : '' }}">Laporan Stok Bahan</a>
            <a href="/admin/transaksi" class="nav-link {{ request()->is('admin/transaksi') ? 'active' : '' }}">Data Transaksi</a>
        </div>
        
        @if(request()->is('admin/transaksi'))
        <div class="profile-block" style="margin-bottom: 15px;">
            <div class="profile-avatar">AD</div>
            <div style="line-height: 1.2;">
                <strong style="font-size: 14px; display: block;">Admin</strong>
                <span style="font-size: 11px; color: #666;">Embun Cafe Management</span>
            </div>
        </div>
        @endif

        <div style="border-top: 1px solid #ddd; padding-top: 15px;">
            <a href="#" class="nav-link" style="color: #555;">⚙️ Settings</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #dc3545; text-decoration: none; font-weight: bold; display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 8px; transition: 0.2s;" onmouseover="this.style.background='#fce8e6'" onmouseout="this.style.background='transparent'">
                🚪 Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="position: relative; width: 400px;">
                <span style="position: absolute; left: 12px; top: 10px; color: #aaa;">🔍</span>
                <input type="text" id="global-search-input" class="global-search" placeholder="Cari data di tabel..." autocomplete="off">
            </div>
            <div style="color: #666; font-size: 12px; display: flex; align-items: center; gap: 15px;">
                <span style="background: #e6f4ea; border: 1px solid #b7e1cd; color: #1e8e3e; padding: 6px 12px; border-radius: 20px; font-weight: bold; display: flex; align-items: center; gap: 6px;">
                    <div style="width: 8px; height: 8px; background: #1e8e3e; border-radius: 50%; box-shadow: 0 0 5px #1e8e3e;"></div> Sistem Online
                </span>
            </div>
        </div>

        @yield('content')
    </div>

    <script>
        // Pakai 'var' agar tidak terjadi tabrakan (Crash) dengan variabel API_URL di halaman lain
        if (typeof API_URL === 'undefined') {
            var API_URL = 'http://127.0.0.1:8000/api';
        }
        
        // Logika Global Search Bar Persis Milikmu (Frontend Filtering)
        document.addEventListener('DOMContentLoaded', () => {
            setupGlobalSearch();
        });

        function setupGlobalSearch() {
            const searchInput = document.getElementById('global-search-input');
            
            if(searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const filter = searchInput.value.toLowerCase();
                    
                    // Otomatis mencari semua tag <tbody> di halaman yang sedang aktif
                    const tableBodies = document.getElementsByTagName('tbody');

                    // Loop untuk setiap tabel yang ada di halaman
                    for (let t = 0; t < tableBodies.length; t++) {
                        const rows = tableBodies[t].getElementsByTagName('tr');

                        // Loop untuk menyembunyikan/menampilkan baris sesuai ketikan
                        for (let i = 0; i < rows.length; i++) {
                            const rowText = rows[i].innerText.toLowerCase();
                            if (rowText.includes(filter)) {
                                rows[i].style.display = '';
                            } else {
                                rows[i].style.display = 'none';
                            }
                        }
                    }
                });
            }
        }
    </script>
    
    @yield('scripts')
</body>
</html>