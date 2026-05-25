@extends('layouts.admin')

@section('title', 'Laporan Stok Bahan')

@section('content')
    <style>
        /* Sembunyikan elemen tertentu saat di-export ke PDF */
        @media print {
            .no-print { display: none !important; }
        }
        /* Style untuk transisi warna tombol kategori */
        .btn-kategori { transition: 0.3s; cursor: pointer; }
    </style>

    <div id="pdf-area">
        <div id="pdf-header" class="no-print" style="display: none; text-align: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #4c7c5f;">LAPORAN STOK BAHAN - EMBUN CAFE</h2>
            <p style="margin: 5px 0 0 0; color: #888; font-size: 12px;">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
            <hr style="border: 1px dashed #ddd; margin-top: 15px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 15px; margin-bottom: 25px;">
            <div class="card" style="background: #fdf5f5; border: 1px solid #fce8e6;">
                <p style="color: #dc3545; font-size: 11px; margin: 0; font-weight: bold;">⚠️ CRITICAL</p>
                <h2 id="val-stok-habis" style="margin: 10px 0 5px 0; color: #dc3545; font-size: 28px;">00</h2>
                <p style="color: #dc3545; font-size: 12px; margin: 0;">Bahan Habis</p>
            </div>
            <div class="card" style="background: #fffdf5; border: 1px solid #fdf5d3;">
                <p style="color: #856404; font-size: 11px; margin: 0; font-weight: bold;">❗ LOW STOCK</p>
                <h2 id="val-stok-hampir" style="margin: 10px 0 5px 0; color: #856404; font-size: 28px;">00</h2>
                <p style="color: #856404; font-size: 12px; margin: 0;">Hampir Habis</p>
            </div>
            <div class="card" style="background: #f5fbf7; border: 1px solid #e6f4ea; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="color: #1e8e3e; font-size: 11px; margin: 0; font-weight: bold;">✔️ SAFE</p>
                    <h2 id="val-stok-aman" style="margin: 10px 0 5px 0; color: #1e8e3e; font-size: 28px;">00</h2>
                    <p style="color: #1e8e3e; font-size: 12px; margin: 0;">Bahan Tersedia Aman</p>
                </div>
                <div style="text-align: right;">
                    <p style="color: #888; font-size: 11px; margin: 0;">Total Inventaris</p>
                    <h2 id="val-stok-total" style="margin: 5px 0 0 0; font-size: 20px; color: #333;">0 Item</h2>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 25px;">
            <div class="no-print" style="display: flex; justify-content: space-between; margin-bottom: 20px; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div style="display: flex; gap: 15px; align-items: center;">
                    <h3 style="margin: 0; border-right: 2px solid #eee; padding-right: 15px;">Detail Persediaan</h3>
                    
                    <div style="display: flex; gap: 8px;" id="kategori-buttons">
                        <button onclick="setKategori('Semua', this)" class="btn-kategori active" style="background: #4c7c5f; color: white; border: none; padding: 6px 15px; border-radius: 20px; font-size: 12px;">Semua</button>
                        <button onclick="setKategori('Kopi', this)" class="btn-kategori" style="background: white; border: 1px solid #ddd; color: #555; padding: 6px 15px; border-radius: 20px; font-size: 12px;">Kopi</button>
                        <button onclick="setKategori('Susu & Krim', this)" class="btn-kategori" style="background: white; border: 1px solid #ddd; color: #555; padding: 6px 15px; border-radius: 20px; font-size: 12px;">Susu & Krim</button>
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <select id="filter-status" onchange="loadStokData()" style="border: 1px solid #ddd; background: white; padding: 8px 15px; border-radius: 5px; color: #555; outline: none; cursor: pointer; font-size: 13px;">
                        <option value="all">⚙️ Semua Status</option>
                        <option value="Aman">✔️ Aman</option>
                        <option value="Hampir Habis">❗ Hampir Habis</option>
                        <option value="Habis">⚠️ Habis</option>
                    </select>
                    
                    <button onclick="exportToPDF()" style="border: 1px solid #ddd; background: white; color: #555; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: bold;">📥 Ekspor PDF</button>
                </div>
            </div>

            <table id="stok-table">
                <thead>
                    <tr>
                        <th>Bahan Baku</th><th>Kategori</th><th>Stok Saat Ini</th><th>Stok Minimum</th><th>Status</th><th>Update Terakhir</th>
                    </tr>
                </thead>
                <tbody id="stok-tbody">
                    <tr><td colspan="6" style="text-align: center; padding: 20px;">⏳ Memuat persediaan...</td></tr>
                </tbody>
            </table>

            <div id="stok-empty" style="display: none; text-align: center; padding: 60px 0;">
                <div style="width: 70px; height: 70px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; font-size: 30px;">📦</div>
                <h3 style="margin: 0 0 5px 0;">Data stok bahan kosong</h3>
                <p style="color: #888; font-size: 14px; margin: 0;">Tidak ada bahan baku yang cocok dengan filter pencarian.</p>
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
                <p style="font-size: 10px; color: #aaa; margin: 0;">Terakhir diverifikasi secara sistem pada {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    if (typeof ADMIN_API_URL === 'undefined') {
        var ADMIN_API_URL = 'http://127.0.0.1:8000/api';
    }
    const adminToken = localStorage.getItem('embun_token');
    
    // Variabel penampung state kategori aktif
    let currentKategori = 'Semua';

    document.addEventListener('DOMContentLoaded', () => {
        loadStokData();
    });

    // Fungsi untuk ganti kategori saat tombol diklik
    window.setKategori = function(kategori, element) {
        currentKategori = kategori;
        
        // Reset warna semua tombol
        const buttons = document.querySelectorAll('.btn-kategori');
        buttons.forEach(btn => {
            btn.style.background = 'white';
            btn.style.color = '#555';
            btn.style.border = '1px solid #ddd';
        });

        // Set warna tombol yang sedang aktif
        element.style.background = '#4c7c5f';
        element.style.color = 'white';
        element.style.border = 'none';

        loadStokData(); // Muat ulang data
    }

    // Fungsi utama tarik data (Terhubung ke API yang baru)
    window.loadStokData = async function() {
        const status = document.getElementById('filter-status').value;

        const tbody = document.getElementById('stok-tbody');
        const tableContainer = document.getElementById('stok-table');
        const emptyState = document.getElementById('stok-empty');

        try {
            const response = await fetch(`${ADMIN_API_URL}/admin/laporan-stok/data?kategori=${encodeURIComponent(currentKategori)}&status=${status}`, {
                headers: { 'Authorization': `Bearer ${adminToken}`, 'Accept': 'application/json' }
            });

            const result = await response.json();
            
            if (response.ok) {
                const metrics = result.metrics || {};
                const items = result.table_data || [];

                // 1. Update Metrik Atas (Tambahkan '0' di depan angka < 10)
                document.getElementById('val-stok-habis').innerText = (metrics.habis < 10 ? '0' : '') + (metrics.habis || 0);
                document.getElementById('val-stok-hampir').innerText = (metrics.hampir_habis < 10 ? '0' : '') + (metrics.hampir_habis || 0);
                document.getElementById('val-stok-aman').innerText = (metrics.aman < 10 ? '0' : '') + (metrics.aman || 0);
                document.getElementById('val-stok-total').innerText = `${metrics.total || 0} Item`;

                // 2. Update Tabel Laporan
                if (items.length > 0) {
                    tableContainer.style.display = 'table';
                    emptyState.style.display = 'none';
                    tbody.innerHTML = '';
                    
                    items.forEach(item => {
                        let statusBadge = '';
                        let textColor = '';
                        
                        // Konversi Badge Sesuai String dari Backend
                        if (item.status === 'Habis') {
                            statusBadge = '<span class="badge badge-danger" style="background: #fce8e6; color: #d93025; padding: 4px 10px; border-radius: 15px; font-weight: bold; font-size: 11px;">• HABIS</span>';
                            textColor = 'color: #dc3545;';
                        } else if (item.status === 'Hampir Habis') {
                            statusBadge = '<span class="badge badge-warning" style="background: #fdf5d3; color: #856404; padding: 4px 10px; border-radius: 15px; font-weight: bold; font-size: 11px;">• MENIPIS</span>';
                            textColor = 'color: #d39e00;';
                        } else {
                            statusBadge = '<span class="badge badge-success" style="background: #e6f4ea; color: #1e8e3e; padding: 4px 10px; border-radius: 15px; font-weight: bold; font-size: 11px;">• AMAN</span>';
                            textColor = 'color: #2e5a40;';
                        }

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="display: flex; align-items: center; gap: 10px;">
                                <span style="background: #eee; padding: 5px; border-radius: 50%;">📦</span>
                                <div><strong style="color: #444;">${item.item_name}</strong></div>
                            </td>
                            <td><span style="background: #eee; padding: 4px 8px; border-radius: 4px; font-size: 11px; color: #666;">${item.category}</span></td>
                            <td style="${textColor}"><strong>${item.quantity} ${item.unit}</strong></td>
                            <td style="color: #888;">${item.minimum_stock} ${item.unit}</td>
                            <td>${statusBadge}</td>
                            <td style="color: #aaa; font-size: 12px;">${item.updated_at}</td>
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
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: red; padding: 20px;">Koneksi terputus.</td></tr>';
        }
    };

    // Fungsi Ekspor ke PDF
    window.exportToPDF = function() {
        const element = document.getElementById('pdf-area');
        const pdfHeader = document.getElementById('pdf-header');

        // Munculkan kop surat PDF sesaat
        pdfHeader.style.display = 'block';
        pdfHeader.classList.remove('no-print');

        const opt = {
            margin:       0.3,
            filename:     'Laporan_Stok_EmbunCafe.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            // Sembunyikan kop surat lagi setelah selesai diunduh
            pdfHeader.style.display = 'none';
            pdfHeader.classList.add('no-print');
        });
    }
</script>
@endsection
