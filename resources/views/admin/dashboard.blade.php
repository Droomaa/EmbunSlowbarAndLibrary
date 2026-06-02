@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
    <style>
        @media print {
            .no-print { display: none !important; }
        }
        /* Style baru untuk tombol toggle grafik */
        .chart-btn { border: none; padding: 6px 15px; font-size: 12px; cursor: pointer; transition: 0.2s; }
        .chart-btn.active { background: #ddd; font-weight: bold; color: #333; border-radius: 20px; }
        .chart-btn.inactive { background: transparent; font-weight: normal; color: #888; border-radius: 20px; }
        
        /* Style untuk dropdown filter */
        .filter-select { border: 1px solid #ddd; background: white; padding: 6px 15px; border-radius: 5px; cursor: pointer; font-size: 13px; outline: none; }
    </style>

    <div id="pdf-area">
        
        <div id="pdf-header" style="display: none; text-align: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #4c7c5f;">EMBUN CAFE - ADMIN DASHBOARD</h2>
            <p style="margin: 5px 0 0 0; color: #888; font-size: 12px;">Tanggal Unduh: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
            <hr style="border: 1px dashed #ddd; margin-top: 15px;">
        </div>

        <div class="grid-4">
            <div class="card">
                <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">Total Penjualan</p>
                <h2 style="margin: 10px 0 5px 0;" id="val-penjualan">Rp 0</h2>
                <p style="color: #aaa; font-size: 11px; margin: 0; font-style: italic;">Dari transaksi selesai</p>
            </div>
            
            <div class="card">
                <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">Total Transaksi</p>
                <h2 style="margin: 10px 0 5px 0;" id="val-transaksi">0</h2>
                <p style="color: #aaa; font-size: 11px; margin: 0; font-style: italic;">Keseluruhan waktu</p>
            </div>
            
            <div class="card">
                <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">Stok Kritis</p>
                <h2 style="margin: 10px 0 5px 0;" id="val-stokkritis">0 Bahan</h2>
                <p style="color: #aaa; font-size: 11px; margin: 0; font-style: italic;">Perlu segera dipesan</p>
            </div>
            
            <div class="card" style="background: #4c7c5f; color: white;">
                <p style="font-size: 12px; margin: 0; display: flex; align-items: center; gap: 5px;">⭐ Top Performer</p>
                <p style="font-size: 11px; opacity: 0.8; margin: 15px 0 5px 0;">Produk Terlaris</p>
                <h3 style="margin: 0 0 10px 0;">Caramel Macchiato</h3>
                <span style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 15px; font-size: 12px;">Menu Andalan</span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2.5fr 1fr; gap: 15px; margin-bottom: 25px;">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;" id="chart-title">Statistik Penjualan 7 Hari</h3>
                    <div style="background: #f0f0f0; border-radius: 20px; display: flex; overflow: hidden;" class="no-print">
                        <button id="btn-mingguan" class="chart-btn active" onclick="switchChart('weekly')">Mingguan</button>
                        <button id="btn-bulanan" class="chart-btn inactive" onclick="switchChart('monthly')">Bulanan</button>
                    </div>
                </div>
                
                <div id="sales-chart-container" style="height: 200px; background: #e8ece9; border-radius: 8px; display: flex; align-items: flex-end; padding: 10px; gap: 10px; justify-content: space-between;">
                    <p style="width: 100%; text-align: center; color: #888;">⏳ Memuat grafik...</p>
                </div>
                <div id="chart-days" style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 10px; color: #888; text-transform: uppercase;">
                </div>
            </div>
            
            <div class="card" style="background: #fcfbf9;">
                <h4 style="margin-top: 0; margin-bottom: 20px;">Laporan Stok Kritis</h4>
                
                <div id="critical-stock-list" style="min-height: 120px;">
                    <p style="text-align: center; color: #888; font-size: 12px;">⏳ Memeriksa gudang...</p>
                </div>

                <button class="no-print" onclick="window.location.href='/admin/stok'" style="width: 100%; padding: 10px; border: 1px solid #ddd; background: white; border-radius: 20px; margin-top: 20px; cursor: pointer; color: #555; transition: 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='white'">Lihat Semua Stok</button>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">Data Transaksi Terbaru</h3>
                <div style="display: flex; gap: 10px;" class="no-print" id="action-buttons">
                    <select id="filter-trx" class="filter-select" onchange="filterTransactions()">
                        <option value="All">⚙️ Semua Status</option>
                        <option value="Completed">🟢 Selesai (Completed)</option>
                        <option value="Pending">🟡 Tertunda (Pending)</option>
                        <option value="Reject">🔴 Ditolak (Reject)</option>
                    </select>
                    
                    <button onclick="downloadAdminPDF()" style="border: none; background: #4c7c5f; color: white; padding: 6px 15px; border-radius: 5px; cursor: pointer;">📥 Ekspor PDF</button>
                </div>
            </div>

            <table id="dashboard-trx-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee; font-size: 11px; color: #888; text-transform: uppercase;">
                        <th style="padding: 10px 5px;">ID Transaksi</th>
                        <th style="padding: 10px 5px;">Pelanggan</th>
                        <th style="padding: 10px 5px;">Waktu</th>
                        <th style="padding: 10px 5px;">Status</th>
                        <th style="padding: 10px 5px;">Total</th>
                        <th style="padding: 10px 5px;" class="no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody id="trx-tbody" style="font-size: 13px; color: #555;">
                    <tr><td colspan="6" style="text-align:center; padding: 20px;">⏳ Memuat data transaksi...</td></tr>
                </tbody>
            </table>

            <div id="dashboard-trx-empty" style="display: none; text-align: center; padding: 40px 0;">
                <div style="width: 60px; height: 60px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; font-size: 24px; color: #aaa;">🔍</div>
                <h3 style="margin: 0 0 5px 0;">Tidak ada transaksi</h3>
                <p style="color: #888; font-size: 14px; margin: 0;">Belum ada transaksi yang sesuai dengan filter.</p>
            </div>

            <div class="no-print" style="text-align: center; margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                <a href="/admin/transaksi" style="color: #4c7c5f; text-decoration: none; font-size: 13px; font-weight: bold;">Lihat Seluruh Riwayat Transaksi</a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    // Variabel Penampung Data Global
    let weeklyData = [];
    let monthlyData = [];
    let allRecentTrx = [];

    const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    const formatRupiahPendek = (angka) => {
        if(angka >= 1000000) return 'Rp ' + (angka/1000000).toFixed(1) + 'M';
        if(angka >= 1000) return 'Rp ' + (angka/1000).toFixed(0) + 'K';
        return 'Rp ' + angka;
    };

    document.addEventListener('DOMContentLoaded', async () => {
        // ✅ API_URL dan Token DIPINDAHKAN KE DALAM SINI AGAR TIDAK CRASH DENGAN LAYOUT
        const API_URL = 'http://127.0.0.1:8000/api';
        const token = localStorage.getItem('embun_token');

        try {
            // Memanggil Endpoint API Dashboard Admin milikmu
            const response = await fetch(`${API_URL}/admin/dashboard/data`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });

            const result = await response.json();
            
            if (response.ok) {
                // Render Data Utama
                document.getElementById('val-penjualan').innerText = formatRupiah(result.total_penjualan || 0);
                document.getElementById('val-transaksi').innerText = result.total_transaksi || 0;
                document.getElementById('val-stokkritis').innerText = `${result.stok_kritis_count || 0} Bahan`;

                // Simpan data ke variabel global untuk Filter
                weeklyData = result.grafik_mingguan || [];
                monthlyData = result.grafik_bulanan || [];
                allRecentTrx = result.transaksi_terbaru || [];

                // Render Chart & Tabel Default
                renderChart(weeklyData);
                renderTransactions(allRecentTrx);
                renderStock(result.stok_kritis_list);
            } else {
                console.error("Gagal mengambil data dari server");
            }
        } catch (error) {
            console.error("Kesalahan jaringan:", error);
        }
    });

    // --- LOGIKA GRAFIK ---
    window.switchChart = function(type) {
        const btnWeek = document.getElementById('btn-mingguan');
        const btnMonth = document.getElementById('btn-bulanan');
        const title = document.getElementById('chart-title');

        if(type === 'weekly') {
            btnWeek.className = 'chart-btn active';
            btnMonth.className = 'chart-btn inactive';
            title.innerText = 'Statistik Penjualan 7 Hari';
            renderChart(weeklyData);
        } else {
            btnWeek.className = 'chart-btn inactive';
            btnMonth.className = 'chart-btn active';
            title.innerText = 'Statistik Penjualan 6 Bulan';
            renderChart(monthlyData);
        }
    }

    function renderChart(dataArr) {
        const chartContainer = document.getElementById('sales-chart-container');
        const chartDays = document.getElementById('chart-days');
        
        chartContainer.innerHTML = '';
        chartDays.innerHTML = '';
        
        if (dataArr && dataArr.length > 0) {
            const maxSales = Math.max(...dataArr.map(d => parseFloat(d.total)), 1);

            dataArr.forEach(data => {
                const totalNum = parseFloat(data.total);
                const heightPct = (totalNum / maxSales) * 100;
                const isHighest = totalNum === maxSales && totalNum > 0;
                
                const barColor = isHighest ? '#4c7c5f' : '#c5d4cc';
                const tooltip = isHighest ? `<span style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); color: #4c7c5f; font-size: 10px; font-weight: bold; white-space: nowrap;">${formatRupiahPendek(totalNum)}</span>` : '';

                chartContainer.innerHTML += `
                    <div style="width: 100%; height: ${heightPct || 5}%; background: ${barColor}; border-radius: 4px 4px 0 0; position: relative; transition: height 0.5s ease;">
                        ${tooltip}
                    </div>
                `;
                chartDays.innerHTML += `<span style="width: 100%; text-align: center;">${data.label}</span>`;
            });
        } else {
            chartContainer.innerHTML = '<p style="width: 100%; text-align: center; color: #888;">Tidak ada data penjualan.</p>';
        }
    }

    // --- LOGIKA TABEL TRANSAKSI & FILTER ---
    window.filterTransactions = function() {
        const status = document.getElementById('filter-trx').value;
        if(status === 'All') {
            renderTransactions(allRecentTrx);
        } else {
            const filtered = allRecentTrx.filter(trx => trx.status === status);
            renderTransactions(filtered);
        }
    }

    function renderTransactions(data) {
        const tbody = document.getElementById('trx-tbody');
        const tableContainer = document.getElementById('dashboard-trx-table');
        const emptyState = document.getElementById('dashboard-trx-empty');

        if (data && data.length > 0) {
            tableContainer.style.display = 'table';
            emptyState.style.display = 'none';
            tbody.innerHTML = '';
            
            data.forEach(trx => {
                const dateObj = new Date(trx.created_at);
                const timeString = `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                const statusColor = trx.status === 'Completed' ? 'color: #1e8e3e;' : (trx.status === 'Reject' ? 'color: #e74c3c;' : 'color: #856404;');
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="padding: 10px 5px; color: #4c7c5f; font-weight: bold;">#EMB-${trx.order_id}</td>
                    <td style="padding: 10px 5px;">
                        <span style="background: #eee; padding: 4px; border-radius: 50%; font-size: 10px; margin-right: 5px;">${(trx.customer_name || 'W').charAt(0).toUpperCase()}</span>
                        ${trx.customer_name || 'Walk-in'}
                    </td>
                    <td style="padding: 10px 5px; color: #888; font-size: 13px;">${timeString} WIB</td>
                    <td style="padding: 10px 5px;"><span style="font-weight: bold; font-size: 11px; ${statusColor}">• ${trx.status}</span></td>
                    <td style="padding: 10px 5px;"><strong>${formatRupiah(trx.total_price)}</strong></td>
                    <td style="padding: 10px 5px;" class="no-print"><a href="/admin/transaksi" style="color: #aaa; text-decoration: none; cursor: pointer;" title="Lihat Detail">👁️</a></td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tableContainer.style.display = 'none';
            emptyState.style.display = 'block';
        }
    }

    // --- LOGIKA STOK ---
    function renderStock(stockListArray) {
        const stockList = document.getElementById('critical-stock-list');
        stockList.innerHTML = '';
        
        if (stockListArray && stockListArray.length > 0) {
            stockListArray.slice(0, 4).forEach(item => {
                const qty = parseFloat(item.quantity);
                const isDanger = qty <= 100;
                const barColor = isDanger ? '#dc3545' : '#d39e00';
                const textColor = isDanger ? '#dc3545' : '#856404';
                const barWidth = Math.max((qty / 500) * 100, 5);
                
                stockList.innerHTML += `
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: bold; margin-bottom: 5px;">
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;" title="${item.item_name}">📦 ${item.item_name}</span>
                            <span style="color: ${textColor};">${qty} ${item.unit}</span>
                        </div>
                        <div style="width: 100%; background: #eee; height: 6px; border-radius: 3px;">
                            <div style="width: ${barWidth}%; background: ${barColor}; height: 100%; border-radius: 3px; transition: width 1s;"></div>
                        </div>
                    </div>
                `;
            });
        } else {
            stockList.innerHTML = '<div style="text-align:center; padding: 30px 0; color: #1e8e3e; font-weight: bold;">✅ Semua stok aman.</div>';
        }
    }

    // --- EKSPOR PDF ---
    window.downloadAdminPDF = function() {
        const element = document.getElementById('pdf-area');
        const pdfHeader = document.getElementById('pdf-header');
        const btnGroup = document.getElementById('action-buttons');

        btnGroup.style.display = 'none';
        pdfHeader.style.display = 'block';

        const opt = {
            margin:       0.3,
            filename:     'Embun_Admin_Dashboard.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            btnGroup.style.display = 'flex';
            pdfHeader.style.display = 'none';
        });
    }
</script>
@endsection
