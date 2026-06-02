<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operational Dashboard - Embun Cafe</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; background-color: #f8f9f6; color: #333; }
        .sidebar { width: 250px; background-color: #f4f3ed; padding: 20px; height: 100vh; border-right: 1px solid #ddd; display: flex; flex-direction: column; box-sizing: border-box; }
        .nav-link { display: flex; align-items: center; gap: 10px; padding: 12px 15px; text-decoration: none; color: #555; border-radius: 8px; margin-bottom: 5px; font-weight: 500; transition: 0.2s; }
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
        
        /* Search Bar Style */
        .global-search { padding: 8px 15px 8px 35px; border-radius: 20px; border: 1px solid #ddd; background: #f9f9f9; width: 250px; outline: none; transition: 0.2s; }
        .global-search:focus { border-color: #4c7c5f; box-shadow: 0 0 0 3px rgba(76,124,95,0.15); }
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
            <a href="/karyawan/offline" class="nav-link {{ request()->is('karyawan/offline') ? 'active' : '' }}">Pesanan Offline Masuk</a>
            <a href="/karyawan/kasir" class="nav-link {{ request()->is('karyawan/kasir') ? 'active' : '' }}">Kasir</a>
            {{-- <a href="#" class="nav-link">Pengiriman Stok Bahan</a> --}}
        </div>
        
        <div style="border-top: 1px solid #ddd; padding-top: 15px;">
            <a href="#" class="nav-link">⚙️ Settings</a>
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
            <h3 style="margin: 0; color: #333;">@yield('title', 'Embun Cafe Operations')</h3>
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 8px; color: #aaa;">🔍</span>
                    <input type="text" id="global-search-input" class="global-search" placeholder="Search data..." autocomplete="off">
                </div>
                
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="text-align: right;">
                        <strong id="layout-user-name" style="font-size: 14px; display: block;">Budi Santoso</strong>
                        <span id="layout-user-role" style="font-size: 10px; color: #888;">KITCHEN MANAGER</span>
                    </div>
                    <div id="layout-user-avatar" style="width: 35px; height: 35px; background: #4c7c5f; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">BS</div>
                </div>
            </div>
        </div>
        
        @yield('content')
    </div>

    <script>
        if (typeof API_URL === 'undefined') {
            var API_URL = 'http://127.0.0.1:8000/api';
        }

        // Script Global Search (Mendukung Tabel & Div Card)
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('global-search-input');
            
            if(searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const filter = searchInput.value.toLowerCase();
                    
                    // 1. CARI DI DALAM TABEL (<tbody> > <tr>)
                    const tableBodies = document.getElementsByTagName('tbody');
                    for (let t = 0; t < tableBodies.length; t++) {
                        const rows = tableBodies[t].getElementsByTagName('tr');
                        for (let i = 0; i < rows.length; i++) {
                            const rowText = rows[i].innerText.toLowerCase();
                            rows[i].style.display = rowText.includes(filter) ? '' : 'none';
                        }
                    }

                    // 2. CARI DI DALAM DIV CARD (Elemen dengan class 'searchable-item')
                    const listItems = document.querySelectorAll('.searchable-item');
                    for (let i = 0; i < listItems.length; i++) {
                        const itemText = listItems[i].innerText.toLowerCase();
                        listItems[i].style.display = itemText.includes(filter) ? '' : 'none';
                    }
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
