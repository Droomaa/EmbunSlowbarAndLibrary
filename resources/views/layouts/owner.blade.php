<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - Embun Cafe</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; background-color: #f9f9f6; color: #333; }
        .sidebar { width: 220px; background-color: #f1efea; padding: 20px; height: 100vh; border-right: 1px solid #ddd; }
        .sidebar h2 { color: #2e5a40; margin-bottom: 5px; }
        .sidebar p { font-size: 12px; color: #777; margin-top: 0; margin-bottom: 30px; }
        .nav-link { display: block; padding: 10px 15px; text-decoration: none; color: #555; border-radius: 5px; margin-bottom: 5px; }
        .nav-link.active { background-color: #e2e8e4; font-weight: bold; color: #2e5a40; }
        .nav-link:hover { background-color: #ddd; }
        
        .main-content { flex: 1; padding: 20px; overflow-y: auto; height: 100vh; box-sizing: border-box; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
        
        /* Utility Classes */
        .text-green { color: #28a745; }
        .text-red { color: #dc3545; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { font-size: 12px; color: #888; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Embun Cafe</h2>
        <p>Owner Dashboard</p>
        
        <a href="/owner/dashboard" class="nav-link {{ request()->is('owner/dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="/owner/reports" class="nav-link {{ request()->is('owner/reports') ? 'active' : '' }}">Sales Reports</a>
        <a href="/owner/stock" class="nav-link {{ request()->is('owner/stock') ? 'active' : '' }}">Stock Reports</a>
        <a href="/owner/accounts" class="nav-link {{ request()->is('owner/accounts') ? 'active' : '' }}">Accounts</a>
        <a href="/owner/menu" class="nav-link {{ request()->is('owner/menu') ? 'active' : '' }}">Menu</a>
        <a href="/owner/transactions" class="nav-link {{ request()->is('owner/transactions') ? 'active' : '' }}">Transactions</a>
        
        <div style="margin-top: 50px;">
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
            <h3>@yield('title')</h3>
            <div>
                <input type="text" id="global-search-input" placeholder="Search data..." style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; outline: none; width: 250px;">
            </div>
        </div>

        @yield('content')
        
    </div>

    <script>
        const API_URL = 'http://127.0.0.1:8000/api';
        
        // Logika Global Search Bar
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

        function logout() {
            localStorage.removeItem('embun_token');
            alert('Logout berhasil!');
            window.location.href = '/';
        }
    </script>
    
    @yield('scripts')

</body>
</html>
