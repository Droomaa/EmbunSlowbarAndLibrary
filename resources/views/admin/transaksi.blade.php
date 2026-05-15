@extends('layouts.admin')

@section('title', 'Data Transaksi')

@section('content')
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px;">
            <button style="background: #4c7c5f; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer;">+ Catat Transaksi Baru</button>
            <button style="background: white; color: #555; border: 1px solid #ddd; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer;">📥 Ekspor Laporan</button>
        </div>
        <div style="display: flex; gap: 10px;">
            <select style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: white; color: #555;">
                <option>📅 Hari Ini: 24 Mei 2024</option>
            </select>
            <button style="background: white; border: 1px solid #ddd; padding: 10px 15px; border-radius: 5px;">⚙️ Filter</button>
        </div>
    </div>

    <div class="grid-4">
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0;">Total Penjualan Hari Ini <span class="badge badge-success" style="float: right;">+12%</span></p>
            <h2 style="margin: 10px 0 0 0;" id="val-trx-total">Rp 0</h2>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0;">Jumlah Transaksi <span style="float: right; color: #aaa; font-size: 10px;">84 Pesanan</span></p>
            <h2 style="margin: 10px 0 0 0;" id="val-trx-count">0</h2>
        </div>
        <div class="card" style="border-bottom: 4px solid #cce0ff;">
            <p style="color: #888; font-size: 11px; margin: 0;">☕ Produk Terlaris</p>
            <h2 style="margin: 10px 0 0 0; font-style: italic; font-weight: normal;">Signature Latte</h2>
        </div>
        <div class="card" style="border-bottom: 4px solid #ffe6cc;">
            <p style="color: #888; font-size: 11px; margin: 0;">👥 Rata-rata Keranjang</p>
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
                Tampilkan <span style="background: #eee; padding: 4px 10px; border-radius: 15px; font-weight: bold;">10</span> data
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
            <p style="color: #888; font-size: 14px; margin: 0;">Mulailah dengan mencatat transaksi baru<br>melalui tombol di atas.</p>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('embun_token');
        const tbody = document.getElementById('history-tbody');
        const tableContainer = document.getElementById('riwayat-table');
        const emptyState = document.getElementById('riwayat-empty');

        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        };

        try {
            const response = await fetch(`${API_URL}/admin/transactions`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (response.ok) {
                const data = result.data;

                // 1. Update Metrik Atas
                document.getElementById('val-trx-total').innerText = formatRupiah(data.total_penjualan_hari_ini || 0);
                document.getElementById('val-trx-count').innerText = data.jumlah_transaksi || 0;
                document.getElementById('val-trx-avg').innerText = formatRupiah(data.rata_keranjang || 0);

                // 2. Update Tabel Riwayat
                if (data.transactions && data.transactions.length > 0) {
                    tableContainer.style.display = 'table';
                    emptyState.style.display = 'none';
                    tbody.innerHTML = '';
                    
                    data.transactions.forEach(trx => {
                        const dateObj = new Date(trx.created_at);
                        const dateString = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                        const timeString = `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        
                        // Gabungkan nama-nama item yang dipesan (Maksimal 2 item biar gak kepanjangan)
                        let itemNames = 'Item tidak diketahui';
                        if (trx.items && trx.items.length > 0) {
                            itemNames = trx.items.slice(0, 2).map(i => `${i.quantity}x ${i.menu ? i.menu.menuName : 'Menu'}`).join(', ');
                            if (trx.items.length > 2) itemNames += '...';
                        }

                        // Untuk sekarang kita set metode pembayaran default 'Tunai' dan Status 'Berhasil'
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="color: #4c7c5f; font-weight: bold;">#TRX-${trx.order_id}</td>
                            <td style="color: #666; font-size: 12px;">${dateString}<br>${timeString} WIB</td>
                            <td><strong>Tunai</strong></td>
                            <td style="color: #666; font-size: 13px;">${itemNames}</td>
                            <td><strong>${formatRupiah(trx.total_price)}</strong></td>
                            <td><span class="badge badge-success">BERHASIL</span></td>
                            <td style="color: #aaa; cursor: pointer;">⋮</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tableContainer.style.display = 'none';
                    emptyState.style.display = 'block';
                }
            }
        } catch (error) {
            console.error("Gagal memuat riwayat transaksi:", error);
        }
    });
</script>
@endsection
