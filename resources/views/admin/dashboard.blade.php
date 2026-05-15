@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
    <div class="grid-4">
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">Total Penjualan <span class="badge badge-success" style="float: right;">+12.5%</span></p>
            <h2 style="margin: 10px 0 5px 0;" id="val-penjualan">Rp 0</h2>
            <p style="color: #aaa; font-size: 11px; margin: 0; font-style: italic;">Dibandingkan bulan lalu</p>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">Total Transaksi <span class="badge" style="background: #f0f0f0; color: #555; float: right;">+48 Trx</span></p>
            <h2 style="margin: 10px 0 5px 0;"id="val-transaksi">0</h2>
            <p style="color: #aaa; font-size: 11px; margin: 0; font-style: italic;">Minggu ini</p>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">Stok Kritis <span class="badge badge-danger" style="float: right;">-5 Item</span></p>
            <h2 style="margin: 10px 0 5px 0;" id="val-stokkritis">0 Bahan</h2>
            <p style="color: #aaa; font-size: 11px; margin: 0; font-style: italic;">Perlu segera dipesan</p>
        </div>
        <div class="card" style="background: #4c7c5f; color: white;">
            <p style="font-size: 12px; margin: 0; display: flex; align-items: center; gap: 5px;">⭐ Top Performer</p>
            <p style="font-size: 11px; opacity: 0.8; margin: 15px 0 5px 0;">Produk Terlaris</p>
            <h3 style="margin: 0 0 10px 0;">Aren Latte Signature</h3>
            <span style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 15px; font-size: 12px;">412 Cup terjual</span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2.5fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0;">Statistik Penjualan 7 Hari</h3>
                <div style="background: #f0f0f0; border-radius: 20px; display: flex; overflow: hidden;">
                    <button style="border: none; background: #ddd; padding: 6px 15px; font-size: 12px; font-weight: bold;">Mingguan</button>
                    <button style="border: none; background: transparent; padding: 6px 15px; font-size: 12px;">Bulanan</button>
                </div>
            </div>
            <div style="height: 200px; background: #e8ece9; border-radius: 8px; display: flex; align-items: flex-end; padding: 10px; gap: 10px; justify-content: space-between;">
                <div style="width: 100%; height: 30%; background: #c5d4cc; border-radius: 4px 4px 0 0;"></div>
                <div style="width: 100%; height: 50%; background: #c5d4cc; border-radius: 4px 4px 0 0;"></div>
                <div style="width: 100%; height: 40%; background: #c5d4cc; border-radius: 4px 4px 0 0;"></div>
                <div style="width: 100%; height: 80%; background: #4c7c5f; border-radius: 4px 4px 0 0; position: relative;">
                    <span style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); color: #4c7c5f; font-size: 10px; font-weight: bold;">Rp 2.1M</span>
                </div>
                <div style="width: 100%; height: 60%; background: #c5d4cc; border-radius: 4px 4px 0 0;"></div>
                <div style="width: 100%; height: 45%; background: #c5d4cc; border-radius: 4px 4px 0 0;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 10px; color: #888; text-transform: uppercase;">
                <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
            </div>
        </div>
        
        <div class="card" style="background: #fcfbf9;">
            <h4 style="margin-top: 0; margin-bottom: 20px;">Laporan Stok Kritis</h4>
            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: bold; margin-bottom: 5px;">
                    <span>☕ Bijih Kopi Arabika</span> <span class="text-red">0.5 Kg</span>
                </div>
                <div style="width: 100%; background: #eee; height: 6px; border-radius: 3px;"><div style="width: 10%; background: #dc3545; height: 100%; border-radius: 3px;"></div></div>
            </div>
            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: bold; margin-bottom: 5px;">
                    <span>🥛 Susu Full Cream</span> <span style="color: #856404;">3 Liter</span>
                </div>
                <div style="width: 100%; background: #eee; height: 6px; border-radius: 3px;"><div style="width: 25%; background: #d39e00; height: 100%; border-radius: 3px;"></div></div>
            </div>
            <button style="width: 100%; padding: 10px; border: 1px solid #ddd; background: white; border-radius: 20px; margin-top: 20px; cursor: pointer; color: #555;">Lihat Semua Stok</button>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0;">Data Transaksi Terbaru</h3>
            <div style="display: flex; gap: 10px;">
                <button style="border: 1px solid #ddd; background: white; padding: 6px 15px; border-radius: 5px;">⚙️ Filter</button>
                <button style="border: none; background: #4c7c5f; color: white; padding: 6px 15px; border-radius: 5px;">📥 Ekspor PDF</button>
            </div>
        </div>

        <table id="dashboard-trx-table">
            <thead>
                <tr>
                    <th>ID Transaksi</th><th>Pelanggan</th><th>Waktu</th><th>Status</th><th>Total</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody id="trx-tbody">
                </tbody>
        </table>

        <div id="dashboard-trx-empty" style="display: none; text-align: center; padding: 40px 0;">
            <div style="width: 60px; height: 60px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; font-size: 24px; color: #aaa;">🔍</div>
            <h3 style="margin: 0 0 5px 0;">Belum ada transaksi hari ini</h3>
            <p style="color: #888; font-size: 14px; margin: 0;">Transaksi yang masuk akan muncul di sini secara otomatis.</p>
        </div>

        <div style="text-align: center; margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
            <a href="/admin/transaksi" style="color: #4c7c5f; text-decoration: none; font-size: 13px; font-weight: bold;">Lihat Seluruh Riwayat Transaksi</a>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('embun_token');
        const tbody = document.getElementById('trx-tbody');
        const tableContainer = document.getElementById('dashboard-trx-table');
        const emptyState = document.getElementById('dashboard-trx-empty');

        // Fungsi format Rupiah
        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        };

        try {
            // Panggil API Backend
            const response = await fetch(`${API_URL}/admin/dashboard-stats`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (response.ok) {
                const data = result.data;

                // 1. Update Kartu Metrik Atas
                document.getElementById('val-penjualan').innerText = formatRupiah(data.total_penjualan || 0);
                document.getElementById('val-transaksi').innerText = data.total_transaksi || 0;
                document.getElementById('val-stokkritis').innerText = `${data.stok_kritis || 0} Bahan`;

                // 2. Update Tabel Transaksi Terbaru
                if (data.recent_transactions && data.recent_transactions.length > 0) {
                    // Munculkan tabel, sembunyikan empty state
                    tableContainer.style.display = 'table';
                    emptyState.style.display = 'none';
                    
                    tbody.innerHTML = ''; // Bersihkan loading
                    
                    data.recent_transactions.forEach(trx => {
                        // Format tanggal sederhana
                        const dateObj = new Date(trx.created_at);
                        const timeString = `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="color: #4c7c5f; font-weight: bold;">#TRX-${trx.order_id}</td>
                            <td><span style="background: #eee; padding: 4px; border-radius: 50%; font-size: 10px; margin-right: 5px;">${trx.customer_name.charAt(0).toUpperCase()}</span> ${trx.customer_name}</td>
                            <td style="color: #888; font-size: 13px;">${timeString} WIB</td>
                            <td><span class="badge badge-success">• Selesai</span></td>
                            <td><strong>${formatRupiah(trx.total_price)}</strong></td>
                            <td style="color: #aaa; cursor: pointer;">⋮</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    // Sembunyikan tabel, munculkan empty state
                    tableContainer.style.display = 'none';
                    emptyState.style.display = 'block';
                }
            }
        } catch (error) {
            console.error("Gagal memuat data dashboard:", error);
        }
    });
</script>
@endsection
