@extends('layouts.admin')

@section('title', 'Data Transaksi')

@section('content')
    <style>
        .filter-input { padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: white; color: #555; outline: none; font-size: 13px; }
        .action-btn { background: transparent; border: none; cursor: pointer; font-size: 16px; margin: 0 3px; opacity: 0.7; transition: 0.2s; }
        .action-btn:hover { opacity: 1; transform: scale(1.1); }
        
        /* Modal Style */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 25px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .btn-modal-save { background: #1e4620; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 15px; }
    </style>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; gap: 15px;">
            <button onclick="exportToCSV()" style="background: white; color: #555; border: 1px solid #ddd; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='white'">📥 Ekspor Laporan</button>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="date" id="filter-date" class="filter-input" onchange="loadDataTransaksi()">
            <select id="filter-status" class="filter-input" onchange="loadDataTransaksi()">
                <option value="all">⚙️ Semua Status</option>
                <option value="Completed">🟢 Selesai</option>
                <option value="Pending">🟡 Pending</option>
                <option value="Reject">🔴 Ditolak</option>
            </select>
            <button onclick="resetFilters()" style="background: white; border: 1px solid #ddd; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 13px;">Reset</button>
        </div>
    </div>

    <div class="grid-4">
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0;">TOTAL PENJUALAN KESELURUHAN</p>
            <h2 style="margin: 10px 0 0 0;" id="val-trx-total">Rp 0</h2>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0;">JUMLAH TRANSAKSI (SUKSES)</p>
            <h2 style="margin: 10px 0 0 0;" id="val-trx-count">0</h2>
        </div>
        <div class="card" style="border-bottom: 4px solid #cce0ff;">
            <p style="color: #888; font-size: 11px; margin: 0;">☕ PRODUK TERLARIS</p>
            <h2 style="margin: 10px 0 0 0; font-style: italic; font-weight: normal;" id="val-trx-bestseller">-</h2>
        </div>
        <div class="card" style="border-bottom: 4px solid #ffe6cc;">
            <p style="color: #888; font-size: 11px; margin: 0;">👥 RATA-RATA KERANJANG</p>
            <h2 style="margin: 10px 0 0 0;" id="val-trx-avg">Rp 0</h2>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
            <div>
                <h3 style="margin: 0;">Riwayat Transaksi</h3>
                <p style="color: #888; font-size: 13px; margin: 5px 0 0 0;">Mengelola catatan transaksi masuk dan keluar</p>
            </div>
            <div style="font-size: 12px; color: #666; display: flex; align-items: center; gap: 5px;">
                Tampilkan
                <select id="filter-limit" style="padding: 4px 8px; border: 1px solid #ddd; border-radius: 5px; outline: none; background: #eee; font-weight: bold;" onchange="loadDataTransaksi()">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                data
            </div>
        </div>

        <table id="riwayat-table">
            <thead>
                <tr>
                    <th>ID Transaksi</th><th>Waktu</th><th>Metode</th><th>Item</th><th>Total</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody id="history-tbody">
            </tbody>
        </table>

        <div id="riwayat-empty" style="display: none; text-align: center; padding: 60px 0;">
            <div style="width: 70px; height: 70px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; font-size: 30px;">🛒</div>
            <h3 style="margin: 0 0 5px 0;">Riwayat transaksi kosong</h3>
            <p style="color: #888; font-size: 14px; margin: 0;">Tidak ada data yang sesuai dengan filter saat ini.</p>
        </div>
    </div>

    <div id="modal-status" class="modal-overlay">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; color: #333;">Update Status</h3>
                <button type="button" onclick="closeModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #888;">×</button>
            </div>
            <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Ubah status untuk pesanan <strong id="modal-order-id"></strong></p>
            
            <form id="form-update-status" onsubmit="submitUpdateStatus(event)">
                <input type="hidden" id="input-trx-id">
                <select id="input-trx-status" class="filter-input" style="width: 100%; padding: 12px; margin-bottom: 10px;">
                    <option value="Pending">🟡 Pending (Tertunda)</option>
                    <option value="Completed">🟢 Completed (Selesai)</option>
                    <option value="Reject">🔴 Reject (Dibatalkan)</option>
                </select>
                
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeModal()" style="background: #f0f0f0; color: #555; border: 1px solid #ddd; padding: 10px; border-radius: 6px; cursor: pointer; width: 100%; margin-top: 15px;">Batal</button>
                    <button type="submit" id="btn-save-status" class="btn-modal-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // ✅ Mengganti nama variabel menjadi ADMIN_API_URL agar tidak tabrakan dengan API_URL di layout!
    const ADMIN_API_URL = 'http://127.0.0.1:8000/api';
    const adminToken = localStorage.getItem('embun_token');
    
    let currentTableData = [];

    document.addEventListener('DOMContentLoaded', () => {
        loadDataTransaksi();
    });

    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    };

    window.loadDataTransaksi = async function() {
        const date = document.getElementById('filter-date').value || 'all';
        const status = document.getElementById('filter-status').value || 'all';
        const limit = document.getElementById('filter-limit').value || 10;

        const tbody = document.getElementById('history-tbody');
        const tableContainer = document.getElementById('riwayat-table');
        const emptyState = document.getElementById('riwayat-empty');

        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">⏳ Mengambil data...</td></tr>';

        try {
            // Menggunakan ADMIN_API_URL dan adminToken
            const response = await fetch(`${ADMIN_API_URL}/admin/data-transaksi/data?date=${date}&status=${status}&limit=${limit}`, {
                headers: { 'Authorization': `Bearer ${adminToken}`, 'Accept': 'application/json' }
            });

            const result = await response.json();
            
            if (response.ok) {
                document.getElementById('val-trx-total').innerText = formatRupiah(result.metrics.total_penjualan || 0);
                document.getElementById('val-trx-count').innerText = result.metrics.jumlah_transaksi || 0;
                document.getElementById('val-trx-avg').innerText = formatRupiah(result.metrics.rata_keranjang || 0);
                document.getElementById('val-trx-bestseller').innerText = result.metrics.produk_terlaris || '-';

                currentTableData = result.table_data || [];

                if (currentTableData.length > 0) {
                    tableContainer.style.display = 'table';
                    emptyState.style.display = 'none';
                    tbody.innerHTML = '';
                    
                    currentTableData.forEach(trx => {
                        const dateObj = new Date(trx.created_at);
                        const dateString = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                        const timeString = `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        
                        let statusBadge = '';
                        if (trx.status === 'Completed') {
                            statusBadge = '<span class="badge badge-success">SELESAI</span>';
                        } else if (trx.status === 'Reject') {
                            statusBadge = '<span class="badge" style="background: #fff5f5; color: #dc3545; border: 1px solid #ffe3e3;">BATAL</span>';
                        } else {
                            statusBadge = '<span class="badge" style="background: #fff9e6; color: #d39e00; border: 1px solid #ffebad;">PENDING</span>';
                        }

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="color: #4c7c5f; font-weight: bold;">#TRX-${trx.order_id}</td>
                            <td style="color: #666; font-size: 12px;">${dateString}<br>${timeString} WIB</td>
                            <td><strong>${trx.payment_method}</strong></td>
                            <td style="color: #666; font-size: 13px;">${trx.items_text}</td>
                            <td><strong>${formatRupiah(trx.total_price)}</strong></td>
                            <td>${statusBadge}</td>
                            <td style="text-align: center;">
                                <button class="action-btn" title="Update Status" onclick="openModal('${trx.order_id}', '${trx.status}')">✏️</button>
                                <button class="action-btn" title="Hapus Permanen" onclick="deleteTransaksi('${trx.order_id}')">🗑️</button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tableContainer.style.display = 'none';
                    emptyState.style.display = 'block';
                }
            }
        } catch (error) {
            console.error("Gagal memuat:", error);
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: red;">Koneksi terputus.</td></tr>';
        }
    };

    window.resetFilters = function() {
        document.getElementById('filter-date').value = '';
        document.getElementById('filter-status').value = 'all';
        document.getElementById('filter-limit').value = '10';
        loadDataTransaksi();
    }

    // --- MODAL UPDATE STATUS ---
    window.openModal = function(id, currentStatus) {
        document.getElementById('modal-order-id').innerText = `#TRX-${id}`;
        document.getElementById('input-trx-id').value = id;
        document.getElementById('input-trx-status').value = currentStatus;
        document.getElementById('modal-status').style.display = 'flex';
    }

    window.closeModal = function() {
        document.getElementById('modal-status').style.display = 'none';
    }

    window.submitUpdateStatus = async function(e) {
        e.preventDefault();
        const id = document.getElementById('input-trx-id').value;
        const newStatus = document.getElementById('input-trx-status').value;

        await fetch(`${ADMIN_API_URL}/admin/data-transaksi/${id}`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${adminToken}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        });
        
        closeModal();
        loadDataTransaksi();
    }

    // --- DELETE TRANSAKSI ---
    window.deleteTransaksi = async function(id) {
        if(confirm(`Yakin ingin menghapus transaksi #TRX-${id} secara permanen?`)) {
            await fetch(`${ADMIN_API_URL}/admin/data-transaksi/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${adminToken}` }
            });
            loadDataTransaksi();
        }
    }

    // --- EKSPOR CSV ---
    window.exportToCSV = function() {
        if (currentTableData.length === 0) return alert('Tidak ada data yang bisa diekspor.');

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "ID Transaksi,Waktu,Metode,Item Terjual,Total Harga,Status\n";

        currentTableData.forEach(row => {
            const itemsEscaped = `"${row.items_text}"`;
            csvContent += `#TRX-${row.order_id},${row.created_at},${row.payment_method},${itemsEscaped},${row.total_price},${row.status}\n`;
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "Riwayat_Transaksi_EmbunCafe.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
