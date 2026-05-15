@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="grid-4">
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">💵 Total Penjualan</p>
            <h2 style="margin: 10px 0 5px 0;" id="val-sales-total">Rp 0</h2>
            <p class="text-green" style="font-size: 11px; margin: 0;">↗ +12% dari kemarin</p>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">🛒 Total Transaksi</p>
            <h2 style="margin: 10px 0 5px 0;" id="val-sales-count">0 Pesanan</h2>
            <p class="text-green" style="font-size: 11px; margin: 0;">↗ +5% dari kemarin</p>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">👥 Total Pelanggan</p>
            <h2 style="margin: 10px 0 5px 0;" id="val-sales-customers">0 Orang</h2>
            <p style="color: white; font-size: 11px; margin: 0;">-</p> </div>
        <div class="card">
            <p class="text-red" style="font-size: 11px; margin: 0; text-transform: uppercase;">❌ Dibatalkan</p>
            <h2 style="margin: 10px 0 5px 0;" id="val-sales-canceled">0 Pesanan</h2>
            <p class="text-red" style="font-size: 11px; margin: 0;">↘ -50% dari kemarin</p>
        </div>
    </div>

    <div class="card" style="margin-bottom: 25px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="date" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f6;">
                <span style="color: #888; font-size: 13px;">sampai</span>
                <input type="date" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f6;">
                <button style="background: #4c7c5f; color: white; border: none; padding: 8px 15px; border-radius: 5px;">Terapkan Filter</button>
            </div>
            <div style="display: flex; gap: 10px;">
                <button style="border: 1px solid #ddd; background: white; padding: 8px 15px; border-radius: 5px;">📤 Export CSV</button>
                <button style="border: 1px solid #ddd; background: white; padding: 8px 15px; border-radius: 5px;">📄 Cetak PDF</button>
            </div>
        </div>

        <table id="penjualan-table">
            <thead>
                <tr>
                    <th>No Transaksi</th><th>Waktu</th><th>Pelanggan</th><th>Item Terjual</th><th>Total Harga</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody id="sales-tbody">
            </tbody>
        </table>
        
        <div id="penjualan-empty" style="display: none; text-align: center; padding: 60px 0;">
            <div style="width: 70px; height: 70px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; font-size: 30px;">📄</div>
            <h3 style="margin: 0 0 5px 0;">Tidak ada laporan penjualan ditemukan</h3>
            <p style="color: #888; font-size: 14px; margin: 0 0 15px 0;">Coba ubah filter tanggal atau kata kunci pencarian Anda.</p>
            <button style="border: none; background: transparent; color: #4c7c5f; font-weight: bold; cursor: pointer;">Hapus Semua Filter</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="card">
            <h3 style="margin: 0 0 20px 0;">Item Paling Laris</h3>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">☕</span>
                    <div>
                        <strong style="display: block;">Kopi Susu Gula Aren</strong>
                        <span style="font-size: 11px; color: #888;">Signature Series</span>
                    </div>
                </div>
                <strong style="border-bottom: 2px solid #4c7c5f; padding-bottom: 2px;">42 Terjual</strong>
            </div>
            </div>
        <div class="card" style="background: #4c7c5f; color: white; position: relative; overflow: hidden;">
            <h3 style="margin: 0 0 10px 0;">Insight Penjualan ✨</h3>
            <p style="font-size: 13px; line-height: 1.5; opacity: 0.9; margin-bottom: 20px;">Penjualan kopi meningkat tajam pada pukul 08:00 - 10:00 pagi. Pertimbangkan untuk memberikan promo paket sarapan untuk meningkatkan volume transaksi di jam tersebut.</p>
            <button style="background: rgba(255,255,255,0.9); color: #4c7c5f; border: none; padding: 8px 15px; border-radius: 20px; font-weight: bold; cursor: pointer;">Lihat Analisa Lengkap</button>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('embun_token');
        const tbody = document.getElementById('sales-tbody');
        const tableContainer = document.getElementById('penjualan-table');
        const emptyState = document.getElementById('penjualan-empty');

        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        };

        try {
            const response = await fetch(`${API_URL}/admin/sales-report`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (response.ok) {
                const data = result.data;

                // 1. Update Metrik Atas
                document.getElementById('val-sales-total').innerText = formatRupiah(data.total_penjualan || 0);
                document.getElementById('val-sales-count').innerText = `${data.total_transaksi || 0} Pesanan`;
                document.getElementById('val-sales-customers').innerText = `${data.total_pelanggan || 0} Orang`;
                document.getElementById('val-sales-canceled').innerText = `${data.dibatalkan || 0} Pesanan`;

                // 2. Update Tabel Laporan
                if (data.laporan && data.laporan.length > 0) {
                    tableContainer.style.display = 'table';
                    emptyState.style.display = 'none';
                    tbody.innerHTML = '';
                    
                    data.laporan.forEach(trx => {
                        const dateObj = new Date(trx.created_at);
                        const timeString = `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')} WIB`;
                        
                        // Gabungkan nama item
                        let itemNames = '-';
                        if (trx.items && trx.items.length > 0) {
                            itemNames = trx.items.map(i => `${i.menu ? i.menu.menuName : 'Menu'}`).join(', ');
                            if(itemNames.length > 40) itemNames = itemNames.substring(0, 40) + '...';
                        }

                        const statusBadge = trx.total_price > 0
                            ? '<span class="badge badge-success">SELESAI</span>'
                            : '<span class="badge badge-danger">BATAL</span>';

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="color: #4c7c5f;">#EB-${trx.created_at.substring(0,4)}${trx.order_id.toString().padStart(4, '0')}</td>
                            <td style="color: #666; font-size: 13px;">${timeString}</td>
                            <td><strong>${trx.customer_name}</strong></td>
                            <td style="color: #666; font-size: 13px;">${itemNames}</td>
                            <td><strong>${formatRupiah(trx.total_price)}</strong></td>
                            <td>${statusBadge}</td>
                            <td style="color: #aaa; cursor: pointer;">👁️</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tableContainer.style.display = 'none';
                    emptyState.style.display = 'block';
                }
            }
        } catch (error) {
            console.error("Gagal memuat laporan penjualan:", error);
        }
    });
</script>
@endsection
