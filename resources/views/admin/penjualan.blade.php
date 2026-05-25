@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>

    <div id="pdf-area">
        <div id="pdf-header" style="display: none; text-align: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #4c7c5f;">LAPORAN PENJUALAN EMBUN CAFE</h2>
            <p style="margin: 5px 0 0 0; color: #888; font-size: 12px;">Periode: <span id="pdf-periode">Semua Waktu</span></p>
            <hr style="border: 1px dashed #ddd; margin-top: 15px;">
        </div>

        <div class="grid-4">
            <div class="card">
                <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">💵 Total Penjualan</p>
                <h2 style="margin: 10px 0 5px 0;" id="val-sales-total">Rp 0</h2>
                <p class="text-green" style="font-size: 11px; margin: 0;">Dari transaksi selesai</p>
            </div>
            <div class="card">
                <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">🛒 Total Transaksi</p>
                <h2 style="margin: 10px 0 5px 0;" id="val-sales-count">0 Pesanan</h2>
                <p class="text-green" style="font-size: 11px; margin: 0;">Pesanan Berhasil</p>
            </div>
            <div class="card">
                <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">👥 Total Pelanggan</p>
                <h2 style="margin: 10px 0 5px 0;" id="val-sales-customers">0 Orang</h2>
                <p style="color: white; font-size: 11px; margin: 0;">-</p> 
            </div>
            <div class="card">
                <p class="text-red" style="font-size: 11px; margin: 0; text-transform: uppercase;">❌ Dibatalkan</p>
                <h2 style="margin: 10px 0 5px 0;" id="val-sales-canceled">0 Pesanan</h2>
                <p class="text-red" style="font-size: 11px; margin: 0;">Pesanan Ditolak</p>
            </div>
        </div>

        <div class="card" style="margin-bottom: 25px;">
            <div class="no-print" style="display: flex; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="date" id="start-date" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f6;">
                    <span style="color: #888; font-size: 13px;">sampai</span>
                    <input type="date" id="end-date" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f6;">
                    <button onclick="applyFilter()" style="background: #4c7c5f; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">Terapkan Filter</button>
                    <button onclick="resetFilter()" style="background: #f0f0f0; color: #555; border: 1px solid #ddd; padding: 8px 15px; border-radius: 5px; cursor: pointer;">Reset</button>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button onclick="exportCSV()" style="border: 1px solid #ddd; background: white; padding: 8px 15px; border-radius: 5px; cursor: pointer;">📤 Export CSV</button>
                    <button onclick="exportPDF()" style="border: 1px solid #ddd; background: white; padding: 8px 15px; border-radius: 5px; cursor: pointer;">📄 Cetak PDF</button>
                </div>
            </div>

            <table id="penjualan-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee; font-size: 11px; color: #888; text-transform: uppercase;">
                        <th style="padding: 10px 5px;">No Transaksi</th>
                        <th style="padding: 10px 5px;">Waktu</th>
                        <th style="padding: 10px 5px;">Pelanggan</th>
                        <th style="padding: 10px 5px;">Item Terjual</th>
                        <th style="padding: 10px 5px;">Total Harga</th>
                        <th style="padding: 10px 5px;">Status</th>
                        <th style="padding: 10px 5px;" class="no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody id="sales-tbody" style="font-size: 13px; color: #555;">
                    <tr><td colspan="7" style="text-align:center; padding: 20px;">⏳ Memuat laporan penjualan...</td></tr>
                </tbody>
            </table>
            
            <div id="penjualan-empty" style="display: none; text-align: center; padding: 60px 0;">
                <div style="width: 70px; height: 70px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; font-size: 30px;">📄</div>
                <h3 style="margin: 0 0 5px 0;">Tidak ada laporan penjualan ditemukan</h3>
                <p style="color: #888; font-size: 14px; margin: 0 0 15px 0;">Coba ubah filter tanggal atau kata kunci pencarian Anda.</p>
                <button onclick="resetFilter()" style="border: none; background: transparent; color: #4c7c5f; font-weight: bold; cursor: pointer;">Hapus Semua Filter</button>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="card">
                <h3 style="margin: 0 0 20px 0;">Item Paling Laris</h3>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 20px;">⭐</span>
                        <div>
                            <strong style="display: block;" id="best-seller-name">⏳ Menghitung...</strong>
                            <span style="font-size: 11px; color: #888;" id="best-seller-category">-</span>
                        </div>
                    </div>
                    <strong style="border-bottom: 2px solid #4c7c5f; padding-bottom: 2px;" id="best-seller-count">0 Terjual</strong>
                </div>
            </div>
            
            <div class="card no-print" style="background: #4c7c5f; color: white; position: relative; overflow: hidden;">
                <h3 style="margin: 0 0 10px 0;">Insight Penjualan ✨</h3>
                <p style="font-size: 13px; line-height: 1.5; opacity: 0.9; margin-bottom: 20px;">Pastikan persediaan stok bahan baku produk terlaris Anda selalu aman untuk memaksimalkan keuntungan harian.</p>
                <button onclick="alert('Fitur AI Insight segera hadir!')" style="background: rgba(255,255,255,0.9); color: #4c7c5f; border: none; padding: 8px 15px; border-radius: 20px; font-weight: bold; cursor: pointer;">Lihat Analisa Lengkap</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    let currentTableData = []; // Simpan data untuk Export CSV

    document.addEventListener('DOMContentLoaded', () => {
        // Load data pertama kali tanpa filter
        loadSalesData();
    });

    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    };

    // 1. Fungsi Utama Mengambil Data dari API
    window.loadSalesData = async function(queryString = '') {
        const API_URL = 'http://127.0.0.1:8000/api';
        const token = localStorage.getItem('embun_token');
        
        const tbody = document.getElementById('sales-tbody');
        const tableContainer = document.getElementById('penjualan-table');
        const emptyState = document.getElementById('penjualan-empty');

        try {
            // Memanggil API yang baru kita buat (tambahkan query tanggal jika ada)
            const response = await fetch(`${API_URL}/admin/laporan-penjualan/data${queryString}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (response.ok) {
                // Update 4 Metrik Atas
                document.getElementById('val-sales-total').innerText = formatRupiah(result.metrics.total_penjualan || 0);
                document.getElementById('val-sales-count').innerText = `${result.metrics.total_transaksi || 0} Pesanan`;
                document.getElementById('val-sales-customers').innerText = `${result.metrics.total_pelanggan || 0} Orang`;
                document.getElementById('val-sales-canceled').innerText = `${result.metrics.dibatalkan || 0} Pesanan`;

                // Update Item Paling Laris (Best Seller)
                if (result.best_seller) {
                    document.getElementById('best-seller-name').innerText = result.best_seller.menuName;
                    document.getElementById('best-seller-category').innerText = result.best_seller.category;
                    document.getElementById('best-seller-count').innerText = `${result.best_seller.total_sold} Terjual`;
                } else {
                    document.getElementById('best-seller-name').innerText = "Belum Ada Data";
                    document.getElementById('best-seller-category').innerText = "-";
                    document.getElementById('best-seller-count').innerText = "0 Terjual";
                }

                // Update Tabel Laporan
                currentTableData = result.table_data || [];
                
                if (currentTableData.length > 0) {
                    tableContainer.style.display = 'table';
                    emptyState.style.display = 'none';
                    tbody.innerHTML = '';
                    
                    currentTableData.forEach(trx => {
                        const dateObj = new Date(trx.created_at);
                        const timeString = `${dateObj.getDate().toString().padStart(2,'0')}/${(dateObj.getMonth()+1).toString().padStart(2,'0')} ${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        
                        // Badge status
                        const statusBadge = trx.status === 'Completed'
                            ? '<span style="background: #e6f4ea; color: #1e8e3e; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: bold;">SELESAI</span>'
                            : (trx.status === 'Reject' 
                                ? '<span style="background: #fff5f5; color: #dc3545; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: bold;">BATAL</span>'
                                : '<span style="background: #fff9e6; color: #d39e00; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: bold;">PENDING</span>');

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="padding: 10px 5px; color: #4c7c5f;">#EB-${trx.order_id.toString().padStart(4, '0')}</td>
                            <td style="padding: 10px 5px; color: #666; font-size: 13px;">${timeString}</td>
                            <td style="padding: 10px 5px;"><strong>${trx.customer_name}</strong></td>
                            <td style="padding: 10px 5px; color: #666; font-size: 13px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${trx.items_text}">${trx.items_text}</td>
                            <td style="padding: 10px 5px;"><strong>${formatRupiah(trx.total_price)}</strong></td>
                            <td style="padding: 10px 5px;">${statusBadge}</td>
                            <td style="padding: 10px 5px; color: #aaa; cursor: pointer;" class="no-print" onclick="window.location.href='/admin/transaksi'">👁️</td>
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
            tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: red;">Koneksi terputus.</td></tr>`;
        }
    };

    // 2. Fungsi Filter Tanggal
    window.applyFilter = function() {
        const start = document.getElementById('start-date').value;
        const end = document.getElementById('end-date').value;
        
        if (start && end) {
            document.getElementById('pdf-periode').innerText = `${start} s/d ${end}`;
            loadSalesData(`?start_date=${start}&end_date=${end}`);
        } else {
            alert('Pilih tanggal awal dan akhir terlebih dahulu!');
        }
    };

    window.resetFilter = function() {
        document.getElementById('start-date').value = '';
        document.getElementById('end-date').value = '';
        document.getElementById('pdf-periode').innerText = 'Semua Waktu';
        loadSalesData('');
    };

    // 3. Fungsi Ekspor CSV
    window.exportCSV = function() {
        if (currentTableData.length === 0) {
            alert('Tidak ada data untuk diekspor!');
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,";
        // Header CSV
        csvContent += "No Transaksi,Waktu,Pelanggan,Item Terjual,Total Harga,Status\n";

        // Isi Data
        currentTableData.forEach(row => {
            const itemsEscaped = `"${row.items_text}"`; // Beri kutip agar koma di nama item tidak merusak kolom CSV
            const rowStr = `#EB-${row.order_id},${row.created_at},${row.customer_name},${itemsEscaped},${row.total_price},${row.status}`;
            csvContent += rowStr + "\n";
        });

        // Download aksi
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "Laporan_Penjualan_Embun_Cafe.csv");
        document.body.appendChild(link); 
        link.click();
        document.body.removeChild(link);
    };

    // 4. Fungsi Ekspor PDF (Sama seperti dashboard)
    window.exportPDF = function() {
        const element = document.getElementById('pdf-area');
        const pdfHeader = document.getElementById('pdf-header');

        pdfHeader.style.display = 'block';

        const opt = {
            margin:       0.3,
            filename:     'Laporan_Penjualan_Embun_Cafe.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            pdfHeader.style.display = 'none';
        });
    };
</script>
@endsection
