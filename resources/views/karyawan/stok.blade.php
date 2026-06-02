@extends('layouts.karyawan')

@section('title', 'Data Stok Bahan')

@section('content')
    <style>
        /* Desain Tambahan untuk Modal & Input */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(3px); }
        .modal-content { background: white; padding: 25px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; color: #666; margin-bottom: 5px; font-weight: bold; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none; font-size: 14px; box-sizing: border-box; }
        .form-control:focus { border-color: #4c7c5f; }
        .action-btn { background: transparent; border: none; cursor: pointer; font-size: 16px; transition: 0.2s; opacity: 0.7; }
        .action-btn:hover { opacity: 1; transform: scale(1.2); }
    </style>

    <p style="color: #666; margin-top: -15px; margin-bottom: 25px;">Pantau dan kelola ketersediaan bahan baku operasional harian.</p>

    <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div class="card" style="background: #f4f7f5; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">Total Bahan</p>
                <h2 id="val-stok-total" style="margin: 5px 0 0 0; font-size: 32px;">0</h2>
            </div>
            <span style="color: #1e8e3e; font-size: 12px; font-weight: bold;">Update Real-time</span>
        </div>
        <div class="card" style="background: #fce8e6; border: 1px solid #fad2cf; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #dc3545; font-size: 11px; margin: 0; text-transform: uppercase;">Stok Habis</p>
                <h2 id="val-stok-habis" style="margin: 5px 0 0 0; color: #dc3545; font-size: 32px;">0</h2>
            </div>
            <span style="font-size: 24px; color: #dc3545;">⚠️</span>
        </div>
        <div class="card" style="background: #fffdf5; border: 1px solid #fdf5d3; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #856404; font-size: 11px; margin: 0; text-transform: uppercase;">Stok Menipis</p>
                <h2 id="val-stok-menipis" style="margin: 5px 0 0 0; color: #856404; font-size: 32px;">0</h2>
            </div>
            <span style="font-size: 24px; color: #856404;">❗</span>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; gap: 10px;">
                <select id="filter-kategori" onchange="renderTable()" style="background: white; border: 1px solid #ddd; padding: 8px 15px; border-radius: 20px; color: #555; outline: none; cursor: pointer;">
                    <option value="all">Semua Kategori</option>
                    <option value="Bahan/Material">Bahan / Material</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <select id="filter-status" onchange="renderTable()" style="background: white; border: 1px solid #ddd; padding: 8px 15px; border-radius: 20px; color: #555; outline: none; cursor: pointer;">
                    <option value="all">Status: Semua</option>
                    <option value="Safe">✔️ Aman (Safe)</option>
                    <option value="Low">❗ Menipis (Low)</option>
                    <option value="Out of Stock">⚠️ Habis</option>
                </select>
            </div>
            <button class="btn-primary" onclick="openModal()" style="padding: 10px 20px; border-radius: 20px; font-size: 13px;">+ Tambah Stok</button>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">MATERIAL NAME</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">QUANTITY</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">UNIT</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">STATUS</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: center; font-size: 11px; color: #888;">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="stok-tbody">
                <tr><td colspan="5" style="text-align: center; padding: 20px; color: #888;">⏳ Memuat data stok...</td></tr>
            </tbody>
        </table>
    </div>

    <div style="background: #f4f7f5; padding: 15px; border-radius: 8px; margin-top: 20px; display: flex; gap: 15px; align-items: flex-start;">
        <span style="font-size: 20px;">💡</span>
        <div>
            <strong style="display: block; color: #2e5a40;">Operational Tip</strong>
            <p style="margin: 5px 0 0 0; font-size: 13px; color: #555;">Stok yang berstatus "Low" akan otomatis muncul dalam daftar usulan pesanan pengadaan besok pagi. Pastikan kuantitas tercatat akurat.</p>
        </div>
    </div>

    <div class="modal-overlay" id="modal-stok">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; color: #333;" id="modal-title">Tambah Stok Baru</h3>
                <button onclick="closeModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #888;">×</button>
            </div>
            
            <form id="form-stok" onsubmit="saveStock(event)">
                <input type="hidden" id="input-id"> <div class="form-group">
                    <label>Nama Bahan / Material</label>
                    <input type="text" id="input-nama" class="form-control" placeholder="Contoh: Susu Oat" required>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 2;">
                        <label>Kuantitas (Jumlah)</label>
                        <input type="number" id="input-qty" class="form-control" placeholder="0" step="0.01" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Satuan (Unit)</label>
                        <select id="input-unit" class="form-control" required>
                            <option value="gram">Gram</option>
                            <option value="kg">Kg</option>
                            <option value="ml">Mililiter</option>
                            <option value="liter">Liter</option>
                            <option value="pcs">Pcs</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Batas Minimum (Opsional - Default: 10)</label>
                    <input type="number" id="input-min" class="form-control" placeholder="10">
                </div>
                
                <button type="submit" id="btn-save" class="btn-primary" style="width: 100%; padding: 12px; border-radius: 6px; margin-top: 10px;">Simpan Stok</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    if (typeof API_URL === 'undefined') {
        var API_URL = 'http://127.0.0.1:8000/api';
    }
    const token = localStorage.getItem('embun_token');
    
    // Simpan semua data mentah ke variabel ini agar bisa di-filter tanpa fetch ulang
    let allStockData = [];

    document.addEventListener('DOMContentLoaded', fetchStockData);

    // ==========================================
    // FUNGSI 1: AMBIL DATA DARI SERVER
    // ==========================================
    async function fetchStockData() {
        try {
            const response = await fetch(`${API_URL}/admin/stock-report`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });

            const result = await response.json();
            
            if (response.ok) {
                // Simpan ke wadah global
                allStockData = result.data.items || [];
                // Render tabel & perbarui metrik
                renderTable();
            }
        } catch (error) {
            console.error("Gagal memuat laporan stok bahan:", error);
            document.getElementById('stok-tbody').innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Koneksi terputus.</td></tr>';
        }
    }

    // ==========================================
    // FUNGSI 2: RENDER TABEL & FILTER CERDAS
    // ==========================================
    function renderTable() {
        const tbody = document.getElementById('stok-tbody');
        const filterStatus = document.getElementById('filter-status').value;
        const filterKategori = document.getElementById('filter-kategori').value;
        
        let total = 0, habis = 0, menipis = 0;
        let htmlContent = '';

        // Looping semua data untuk di-filter dan dihitung
        allStockData.forEach(item => {
            const qty = parseFloat(item.quantity);
            const min = parseFloat(item.minimum_stock || 10);
            
            // Tentukan status aslinya
            let status = 'Safe';
            if (qty <= 0) status = 'Out of Stock';
            else if (qty <= min) status = 'Low';

            // Hitung metrik (Hitung semua tanpa peduli filter yang aktif)
            total++;
            if (status === 'Out of Stock') habis++;
            if (status === 'Low') menipis++;

            // FILTER LOGIC
            // Jika kategori yg dipilih bukan 'all' dan tidak cocok, lewati
            if (filterKategori !== 'all' && (item.category || 'Bahan/Material') !== filterKategori) return;
            // Jika status yg dipilih bukan 'all' dan tidak cocok, lewati
            if (filterStatus !== 'all' && status !== filterStatus) return;

            // Render Tampilan Status
            let statusBadge = '';
            let textQtyStyle = '';
            if (status === 'Out of Stock') {
                statusBadge = '<span style="background: #fce8e6; color: #dc3545; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">⚠️ HABIS</span>';
                textQtyStyle = 'color: #dc3545;';
            } else if (status === 'Low') {
                statusBadge = '<span style="background: #fdf5d3; color: #856404; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">❗ LOW</span>';
                textQtyStyle = 'color: #856404;';
            } else {
                statusBadge = '<span style="background: #e6f4ea; color: #1e8e3e; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">✔️ SAFE</span>';
                textQtyStyle = 'color: #333;';
            }

            // Stringify datanya agar gampang dipassing ke parameter fungsi Edit
            const itemJson = encodeURIComponent(JSON.stringify(item));

            htmlContent += `
                <tr>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee;">
                        <strong>${item.item_name}</strong><br>
                        <span style="font-size: 10px; color: #aaa;">Kategori: ${item.category || 'Bahan/Material'}</span>
                    </td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; ${textQtyStyle}"><strong>${qty}</strong></td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; color: #888;">${item.unit}</td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee;">${statusBadge}</td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: center;">
                        <button class="action-btn" title="Edit Stok" onclick="openModal('${itemJson}')">✏️</button>
                        <button class="action-btn" title="Hapus Stok" onclick="deleteStock(${item.id})">🗑️</button>
                    </td>
                </tr>
            `;
        });

        // Update Kartu Metrik
        document.getElementById('val-stok-total').innerText = total;
        document.getElementById('val-stok-habis').innerText = habis;
        document.getElementById('val-stok-menipis').innerText = menipis;

        // Tampilkan ke layar
        if (htmlContent === '') {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px; color: #888;">Tidak ada data yang sesuai filter.</td></tr>';
        } else {
            tbody.innerHTML = htmlContent;
        }
    }


    // ==========================================
    // FUNGSI 3: MODAL DAN FORM (CREATE & UPDATE)
    // ==========================================
    window.openModal = function(itemJsonStr = null) {
        document.getElementById('modal-stok').style.display = 'flex';
        const form = document.getElementById('form-stok');
        
        if (itemJsonStr) {
            // MODE EDIT
            const item = JSON.parse(decodeURIComponent(itemJsonStr));
            document.getElementById('modal-title').innerText = 'Edit Stok Bahan';
            document.getElementById('input-id').value = item.id;
            document.getElementById('input-nama').value = item.item_name;
            document.getElementById('input-qty').value = item.quantity;
            document.getElementById('input-unit').value = item.unit;
            document.getElementById('input-min').value = item.minimum_stock || 10;
        } else {
            // MODE TAMBAH BARU
            document.getElementById('modal-title').innerText = 'Tambah Stok Baru';
            form.reset();
            document.getElementById('input-id').value = '';
        }
    }

    window.closeModal = function() {
        document.getElementById('modal-stok').style.display = 'none';
    }

    window.saveStock = async function(e) {
        e.preventDefault();
        const id = document.getElementById('input-id').value;
        const btn = document.getElementById('btn-save');
        
        btn.innerText = '⏳ Menyimpan...';
        btn.disabled = true;

        const payload = {
            item_name: document.getElementById('input-nama').value,
            quantity: document.getElementById('input-qty').value,
            unit: document.getElementById('input-unit').value,
            minimum_stock: document.getElementById('input-min').value || 10,
            category: 'Bahan/Material' // Default kategori
        };

        // Jika ID ada = PATCH (Edit), jika ID kosong = POST (Tambah Baru)
        const url = id ? `${API_URL}/inventory/${id}` : `${API_URL}/inventory`;
        const method = id ? 'PATCH' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                closeModal();
                fetchStockData(); // Refresh tabel setelah sukses
            } else {
                const err = await response.json();
                alert(`Gagal menyimpan: ${err.message || 'Error dari server'}`);
            }
        } catch (error) {
            alert('Kesalahan koneksi ke server.');
        } finally {
            btn.innerText = 'Simpan Stok';
            btn.disabled = false;
        }
    }

    // ==========================================
    // FUNGSI 4: HAPUS (DELETE)
    // ==========================================
    window.deleteStock = async function(id) {
        if (!confirm('Yakin ingin menghapus bahan baku ini dari sistem?')) return;

        try {
            const response = await fetch(`${API_URL}/inventory/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });

            if (response.ok) {
                fetchStockData(); // Refresh tabel
            } else {
                alert('Gagal menghapus data.');
            }
        } catch (error) {
            alert('Kesalahan koneksi ke server.');
        }
    }
</script>
@endsection
