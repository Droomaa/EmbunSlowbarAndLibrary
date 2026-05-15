@extends('layouts.admin')

@section('title', 'Laporan Stok Bahan')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 15px; margin-bottom: 25px;">
        <div class="card" style="background: #fdf5f5; border: 1px solid #fce8e6;">
            <p style="color: #dc3545; font-size: 11px; margin: 0; font-weight: bold;">⚠️ CRITICAL</p>
            <h2 id="val-stok-habis" style="margin: 10px 0 5px 0; color: #dc3545; font-size: 28px;">0</h2>
            <p style="color: #dc3545; font-size: 12px; margin: 0;">Bahan Habis</p>
        </div>
        <div class="card" style="background: #fffdf5; border: 1px solid #fdf5d3;">
            <p style="color: #856404; font-size: 11px; margin: 0; font-weight: bold;">❗ LOW STOCK</p>
            <h2 id="val-stok-hampir" style="margin: 10px 0 5px 0; color: #856404; font-size: 28px;">0</h2>
            <p style="color: #856404; font-size: 12px; margin: 0;">Hampir Habis</p>
        </div>
        <div class="card" style="background: #f5fbf7; border: 1px solid #e6f4ea; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #1e8e3e; font-size: 11px; margin: 0; font-weight: bold;">✔️ SAFE</p>
                <h2 id="val-stok-aman" style="margin: 10px 0 5px 0; color: #1e8e3e; font-size: 28px;">0</h2>
                <p style="color: #1e8e3e; font-size: 12px; margin: 0;">Bahan Tersedia Aman</p>
            </div>
            <div style="text-align: right;">
                <p style="color: #888; font-size: 11px; margin: 0;">Total Inventaris</p>
                <h2 id="val-stok-total" style="margin: 5px 0 0 0; font-size: 20px; color: #333;">0 Item</h2>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 25px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; align-items: center;">
            <div style="display: flex; gap: 15px; align-items: center;">
                <h3 style="margin: 0; border-right: 2px solid #eee; padding-right: 15px;">Detail Persediaan</h3>
                <div style="display: flex; gap: 8px;">
                    <button style="background: #4c7c5f; color: white; border: none; padding: 6px 15px; border-radius: 20px; font-size: 12px;">Semua</button>
                    <button style="background: white; border: 1px solid #ddd; color: #555; padding: 6px 15px; border-radius: 20px; font-size: 12px;">Kopi</button>
                    <button style="background: white; border: 1px solid #ddd; color: #555; padding: 6px 15px; border-radius: 20px; font-size: 12px;">Susu & Krim</button>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <button style="border: 1px solid #ddd; background: white; padding: 8px 15px; border-radius: 5px;">⚙️ Filter Status</button>
                <button style="border: 1px solid #ddd; background: white; padding: 8px 15px; border-radius: 5px;">📥 Ekspor PDF</button>
            </div>
        </div>

        <table id="stok-table">
            <thead>
                <tr>
                    <th>Bahan Baku</th><th>Kategori</th><th>Stok Saat Ini</th><th>Stok Minimum</th><th>Status</th><th>Update Terakhir</th>
                </tr>
            </thead>
            <tbody id="stok-tbody">
            </tbody>
        </table>

        <div id="stok-empty" style="display: none; text-align: center; padding: 60px 0;">
            <div style="width: 70px; height: 70px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; font-size: 30px;">📦</div>
            <h3 style="margin: 0 0 5px 0;">Data stok bahan belum tersedia</h3>
            <p style="color: #888; font-size: 14px; margin: 0;">Informasi persediaan bahan baku akan muncul setelah<br>diinput oleh Karyawan.</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
        <div class="card" style="background: #f4f7f5;">
            <h4 style="margin: 0 0 10px 0; display: flex; align-items: center; gap: 5px;">📈 Prediksi Kebutuhan Minggu Depan</h4>
            <p style="font-size: 13px; color: #555; line-height: 1.5; margin: 0;">Berdasarkan tren penjualan 7 hari terakhir, stok <strong class="text-green">Susu UHT</strong> dan <strong class="text-green">Caramel Syrup</strong> diperkirakan akan mencapai batas minimum dalam 3 hari ke depan. Disarankan untuk segera melakukan koordinasi pengadaan.</p>
        </div>
        <div class="card" style="background: #fcfbf9;">
            <h4 style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 5px;">📋 Catatan Inventaris Terakhir</h4>
            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #4c7c5f; padding-bottom: 5px; margin-bottom: 5px;">
                <span style="font-size: 13px; color: #555;">Stock Opname Bulanan</span>
                <strong class="text-green" style="font-size: 12px;">SELESAI</strong>
            </div>
            <p style="font-size: 10px; color: #aaa; margin: 0;">Terakhir diverifikasi oleh Manager Area pada 28 Mei 2024</p>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('embun_token');
        const tbody = document.getElementById('stok-tbody');
        const tableContainer = document.getElementById('stok-table');
        const emptyState = document.getElementById('stok-empty');

        try {
            const response = await fetch(`${API_URL}/admin/stock-report`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (response.ok) {
                const data = result.data;

                // 1. Update Metrik Atas (Kasih angka 0 di depan kalau angkanya di bawah 10 biar estetik)
                document.getElementById('val-stok-habis').innerText = data.habis < 10 ? `0${data.habis}` : data.habis;
                document.getElementById('val-stok-hampir').innerText = data.hampir_habis < 10 ? `0${data.hampir_habis}` : data.hampir_habis;
                document.getElementById('val-stok-aman').innerText = data.aman < 10 ? `0${data.aman}` : data.aman;
                document.getElementById('val-stok-total').innerText = `${data.total || 0} Item`;

                // 2. Update Tabel Laporan
                if (data.items && data.items.length > 0) {
                    tableContainer.style.display = 'table';
                    emptyState.style.display = 'none';
                    tbody.innerHTML = '';
                    
                    data.items.forEach(item => {
                        const dateObj = new Date(item.updated_at);
                        const updateString = `${dateObj.getDate()} ${dateObj.toLocaleString('id-ID', { month: 'short' })}, ${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        
                        // Tentukan Status dan Warna berdasarkan kuantitas
                        let statusBadge = '';
                        let textColor = '';
                        if (item.quantity <= 0) {
                            statusBadge = '<span class="badge badge-danger">• HABIS</span>';
                            textColor = 'color: #dc3545;';
                        } else if (item.quantity <= 10) {
                            statusBadge = '<span class="badge badge-warning" style="background: #fdf5d3; color: #856404;">• MENIPIS</span>';
                            textColor = 'color: #d39e00;';
                        } else {
                            statusBadge = '<span class="badge badge-success" style="background: #e6f4ea; color: #1e8e3e;">• AMAN</span>';
                            textColor = 'color: #2e5a40;';
                        }

                        // Buat Baris Tabel
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="display: flex; align-items: center; gap: 10px;">
                                <span style="background: #eee; padding: 5px; border-radius: 50%;">📦</span>
                                <div><strong>${item.item_name}</strong></div>
                            </td>
                            <td><span style="background: #eee; padding: 4px 8px; border-radius: 4px; font-size: 11px;">Bahan/Material</span></td>
                            <td style="${textColor}"><strong>${item.quantity} ${item.unit}</strong></td>
                            <td style="color: #888;">10 ${item.unit}</td>
                            <td>${statusBadge}</td>
                            <td style="color: #aaa; font-size: 12px;">${updateString}</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tableContainer.style.display = 'none';
                    emptyState.style.display = 'block';
                }
            }
        } catch (error) {
            console.error("Gagal memuat laporan stok bahan:", error);
        }
    });
</script>
@endsection
