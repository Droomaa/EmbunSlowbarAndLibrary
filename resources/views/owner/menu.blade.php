@extends('layouts.owner')

@section('title', 'Menu Management')

@section('content')
    <style>
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-title { margin: 0; font-size: 18px; color: #333; }
        .close-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #888; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; color: #555; margin-bottom: 5px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none; box-sizing: border-box; }
        .form-control:focus { border-color: #2e5a40; }
        
        .btn-submit { background: #2e5a40; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-submit:hover { background: #1e4620; }
        
        .action-icon { background: none; border: none; font-size: 16px; cursor: pointer; margin: 0 5px; opacity: 0.7; transition: 0.2s; }
        .action-icon:hover { opacity: 1; transform: scale(1.1); }
    </style>

    <div class="card" style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h3 style="margin: 0; color: #333;">Catalogue Overview</h3>
                <span style="font-size: 13px; color: #888;">Manage your cafe items, pricing, and daily availability.</span>
            </div>
            
            <div style="display: flex; gap: 15px; align-items: center;">
                <select id="filter-category" style="padding: 10px 15px; border-radius: 6px; border: 1px solid #ddd; outline: none; font-size: 13px; cursor: pointer;">
                    <option value="All">All Categories</option>
                    <option value="Coffee">Coffee</option>
                    <option value="Non-Coffee">Non-Coffee</option>
                    <option value="Snacks">Snacks</option>
                    <option value="Meals">Meals</option>
                </select>
                
                <button onclick="openModal('add')" style="background: #2e5a40; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    + Add New Menu
                </button>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; font-size: 11px; color: #888; text-transform: uppercase;">
                    <th style="padding: 15px 5px;">Product</th>
                    <th style="padding: 15px 5px;">Category</th>
                    <th style="padding: 15px 5px;">Price</th>
                    <th style="padding: 15px 5px;">Status</th>
                    <th style="padding: 15px 5px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody id="menu-tbody" style="font-size: 13px; color: #555;">
                <tr><td colspan="5" style="text-align:center; padding: 20px;">⏳ Memuat data...</td></tr>
            </tbody>
        </table>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
        
        <div class="card" style="background: #fff9e6; padding: 20px; border-radius: 12px; border: 1px solid #ffebad;">
            <span style="font-size: 11px; color: #856404; font-weight: bold;">📈 Most Popular</span>
            <h3 id="val-popular-name" style="margin: 10px 0 5px 0; color: #333;">-</h3>
            <span style="font-size: 12px; color: #856404;"><span id="val-popular-sold">0</span> orders this week</span>
        </div>

        <div class="card" style="background: #f9f9f9; padding: 20px; border-radius: 12px; border: 1px solid #eee;">
            <span style="font-size: 11px; color: #888; font-weight: bold;">⚠️ Stock Alerts</span>
            <h3 style="margin: 10px 0 5px 0; color: #333;"><span id="val-stock-alerts">0</span> Items</h3>
            <span style="font-size: 12px; color: #888;">Require immediate restocking</span>
        </div>

        <div class="card" style="background: #f9f9f9; padding: 20px; border-radius: 12px; border: 1px solid #eee;">
            <span style="font-size: 11px; color: #888; font-weight: bold;">📋 Total Catalog</span>
            <h3 style="margin: 10px 0 5px 0; color: #333;"><span id="val-total-catalog">0</span> Items</h3>
            <span style="font-size: 12px; color: #888;">Across all categories</span>
        </div>

    </div>

    <div id="modal-menu" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">Add New Menu</h3>
                <button class="close-btn" onclick="closeModal()">×</button>
            </div>
            
            <form id="menu-form" onsubmit="submitMenu(event)">
                <input type="hidden" id="input-id"> <div class="form-group">
                    <label>Menu Name</label>
                    <input type="text" id="input-name" class="form-control" placeholder="Contoh: Caramel Macchiato" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" id="input-desc" class="form-control" placeholder="Contoh: Classic Espresso Base">
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Category</label>
                        <select id="input-category" class="form-control" required>
                            <option value="Coffee">Coffee</option>
                            <option value="Non-Coffee">Non-Coffee</option>
                            <option value="Snacks">Snacks</option>
                            <option value="Meals">Meals</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Price (Rp)</label>
                        <input type="number" id="input-price" class="form-control" placeholder="32000" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select id="input-status" class="form-control" required>
                        <option value="Available">Available</option>
                        <option value="Unavailable">Unavailable</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-submit" id="btn-save">Save Menu</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const API_URL_MENU = 'http://127.0.0.1:8000/api';
    const token = localStorage.getItem('embun_token');
    
    let allMenus = [];
    let modalMode = 'add'; // 'add' atau 'edit'

    document.addEventListener('DOMContentLoaded', () => {
        loadMenusData();
        // Listener untuk filter dropdown
        document.getElementById('filter-category').addEventListener('change', renderTable);
    });

    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    };

    // Buka Tutup Modal
    window.openModal = function(mode, menuData = null) {
        modalMode = mode;
        const modal = document.getElementById('modal-menu');
        const form = document.getElementById('menu-form');
        const title = document.getElementById('modal-title');

        if (mode === 'add') {
            title.innerText = 'Add New Menu';
            form.reset();
            document.getElementById('input-id').value = '';
        } else if (mode === 'edit' && menuData) {
            title.innerText = 'Edit Menu';
            document.getElementById('input-id').value = menuData.id;
            document.getElementById('input-name').value = menuData.menuName;
            document.getElementById('input-desc').value = menuData.description || '';
            document.getElementById('input-category').value = menuData.category || 'Coffee';
            document.getElementById('input-price').value = menuData.price;
            document.getElementById('input-status').value = menuData.status || 'Available';
        }
        
        modal.style.display = 'flex';
    }

    window.closeModal = function() {
        document.getElementById('modal-menu').style.display = 'none';
    }

    // Load Data Tabel dan Metrik (Bottom Cards)
    async function loadMenusData() {
        try {
            const response = await fetch(`${API_URL_MENU}/owner/menus/data`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await response.json();

            if (response.ok) {
                allMenus = result.menus || [];
                
                // Update 3 Card Bawah
                document.getElementById('val-total-catalog').innerText = result.metrics.total_catalog;
                document.getElementById('val-stock-alerts').innerText = result.metrics.stock_alerts;
                document.getElementById('val-popular-name').innerText = result.metrics.popular_name;
                document.getElementById('val-popular-sold').innerText = result.metrics.popular_sold;

                renderTable();
            }
        } catch (error) {
            console.error("Error:", error);
        }
    }

    // Render Tabel + Fitur Filter Kategori
    function renderTable() {
        const tbody = document.getElementById('menu-tbody');
        const filterVal = document.getElementById('filter-category').value;
        tbody.innerHTML = '';

        // Filter data array
        const filteredMenus = filterVal === 'All' 
            ? allMenus
            : allMenus.filter(m => m.category === filterVal);

        if(filteredMenus.length > 0) {
            filteredMenus.forEach(menu => {
                const isAvailable = menu.status === 'Available';
                const badgeColor = isAvailable ? 'background: #e6f4ea; color: #1e8e3e;' : 'background: #fff5f5; color: #e74c3c; border: 1px solid #ffe3e3;';
                
                // Konversi objek menu jadi string JSON supaya bisa dikirim via parameter onclick (Tombol Edit)
                const menuJson = JSON.stringify(menu).replace(/"/g, '&quot;');

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 35px; height: 35px; border-radius: 50%; background: #eee;"></div>
                            <div>
                                <strong style="display: block; color: #333;">${menu.menuName}</strong>
                                <span style="font-size: 11px; color: #888;">${menu.description || '-'}</span>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">${menu.category || '-'}</td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;"><strong>${formatRupiah(menu.price)}</strong></td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; ${badgeColor}">${menu.status || 'Available'}</span>
                    </td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; text-align: center;">
                        <button class="action-icon" onclick="openModal('edit', ${menuJson})" title="Edit Menu">✏️</button>
                        <button class="action-icon" onclick="deleteMenu(${menu.id})" title="Hapus Menu">🗑️</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: #888; padding: 20px;">Tidak ada menu di kategori ini.</td></tr>`;
        }
    }

    // Fungsi Tambah / Edit Submit
    window.submitMenu = async function(e) {
        e.preventDefault();
        const btnSave = document.getElementById('btn-save');
        btnSave.innerText = 'Menyimpan...';
        btnSave.disabled = true;

        const id = document.getElementById('input-id').value;
        const data = {
            menuName: document.getElementById('input-name').value,
            description: document.getElementById('input-desc').value,
            category: document.getElementById('input-category').value,
            price: document.getElementById('input-price').value,
            status: document.getElementById('input-status').value,
        };

        const url = modalMode === 'add' ? `${API_URL_MENU}/owner/menus/add` : `${API_URL_MENU}/owner/menus/${id}`;
        const method = modalMode === 'add' ? 'POST' : 'PUT';

        try {
            const response = await fetch(url, {
                method: method,
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                closeModal();
                loadMenusData(); // Refresh Data Otomatis
            } else {
                const errData = await response.json();
                alert('GAGAL: ' + (errData.error || 'Pastikan kolom database sesuai.'));
            }
        } catch (error) {
            alert('Terjadi kesalahan koneksi.');
        } finally {
            btnSave.innerText = 'Save Menu';
            btnSave.disabled = false;
        }
    }

    // Fungsi Hapus Menu
    window.deleteMenu = async function(id) {
        if(!confirm('Yakin ingin menghapus menu ini dari katalog?')) return;

        try {
            const response = await fetch(`${API_URL_MENU}/owner/menus/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });

            if (response.ok) {
                loadMenusData(); // Refresh Data Otomatis
            } else {
                alert('Gagal menghapus menu.');
            }
        } catch (error) {
            alert('Terjadi kesalahan koneksi.');
        }
    }
</script>
@endsection
