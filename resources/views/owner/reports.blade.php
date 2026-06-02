@extends('layouts.owner')

@section('title', 'Sales Reports')

@section('content')
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
        
        <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
            <span style="font-size: 11px; color: #888; font-weight: bold; text-transform: uppercase;">Total Revenue</span>
            <h2 id="val-total-revenue" style="margin: 10px 0 15px 0; font-size: 28px; color: #333;">Rp 0</h2>
            <span style="font-size: 11px; color: #1e8e3e; font-weight: bold; background: #e6f4ea; padding: 4px 10px; border-radius: 20px;">Berubah sesuai filter</span>
        </div>

        <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
            <span style="font-size: 11px; color: #888; font-weight: bold; text-transform: uppercase;">Avg. Order Value</span>
            <h2 id="val-avg-order" style="margin: 10px 0 15px 0; font-size: 28px; color: #333;">Rp 0</h2>
            <span style="font-size: 11px; color: #856404; font-weight: bold; background: #fff3cd; padding: 4px 10px; border-radius: 20px;">Berubah sesuai filter</span>
        </div>

        <div class="card" style="background: #1e4620; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); color: white; display: flex; flex-direction: column; justify-content: flex-end;">
            <span style="font-size: 11px; color: #a1c9a8; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;">Cafe Snapshot</span>
            <h2 style="margin: 0; font-size: 24px; font-style: italic;">"Rooted in Flavor"</h2>
        </div>

    </div>

    <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; gap: 15px;">
                <select id="filter-date" style="padding: 10px 15px; border-radius: 6px; border: 1px solid #ddd; outline: none; font-size: 13px; cursor: pointer;">
                    <option value="all">All Dates (Lifetime)</option>
                    <option value="month">This Month</option>
                </select>
                
                <select id="filter-payment" style="padding: 10px 15px; border-radius: 6px; border: 1px solid #ddd; outline: none; font-size: 13px; cursor: pointer;">
                    <option value="all">All Payment Methods</option>
                    <option value="qris">QRIS</option>
                    <option value="tunai">Tunai</option>
                </select>
            </div>
            
            <a href="/owner/reports/export" style="background: #f4f3ed; color: #333; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: bold; border: 1px solid #ddd; display: flex; align-items: center; gap: 8px; transition: 0.2s;">
                📥 Export Report
            </a>
        </div>

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; font-size: 11px; color: #888;">
                    <th style="padding: 15px 5px;">DATE</th>
                    <th style="padding: 15px 5px;">TRANSACTION ID</th>
                    <th style="padding: 15px 5px;">CUSTOMER</th>
                    <th style="padding: 15px 5px;">TOTAL SALES</th>
                    <th style="padding: 15px 5px;">PAYMENT METHOD</th>
                    <th style="padding: 15px 5px; text-align: center;">ACTION</th>
                </tr>
            </thead>
            <tbody id="sales-tbody" style="font-size: 13px; color: #555;">
                <tr><td colspan="6" style="text-align:center; padding: 20px;">⏳ Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    const API_URL_SALES = 'http://127.0.0.1:8000/api';
    const token = localStorage.getItem('embun_token');
    
    // Variabel penampung data asli agar tidak perlu fetch API berulang kali
    let allTransactions = [];

    document.addEventListener('DOMContentLoaded', () => {
        loadSalesReports();

        // Daftarkan event listener agar tabel di-update tiap dropdown diganti
        document.getElementById('filter-date').addEventListener('change', applyFilters);
        document.getElementById('filter-payment').addEventListener('change', applyFilters);
    });

    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    };

    async function loadSalesReports() {
        try {
            const response = await fetch(`${API_URL_SALES}/owner/reports/data`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok) {
                // Simpan semua data ke variabel global
                allTransactions = result.transactions || [];
                // Panggil fungsi filter untuk render pertama kali
                applyFilters();
            }
        } catch (error) {
            console.error("Gagal memuat laporan penjualan:", error);
            document.getElementById('sales-tbody').innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 30px; color: red;">❌ Gagal terhubung ke server.</td></tr>';
        }
    }

    // Fungsi canggih untuk menyaring data dan menghitung ulang metrik
    function applyFilters() {
        const dateFilter = document.getElementById('filter-date').value;
        const paymentFilter = document.getElementById('filter-payment').value.toLowerCase();

        let filteredData = allTransactions;

        // 1. Logika Filter Berdasarkan Waktu (This Month vs Lifetime)
        if (dateFilter === 'month') {
            const today = new Date();
            const currentMonth = today.getMonth();
            const currentYear = today.getFullYear();

            filteredData = filteredData.filter(trx => {
                const trxDate = new Date(trx.created_at);
                return trxDate.getMonth() === currentMonth && trxDate.getFullYear() === currentYear;
            });
        }

        // 2. Logika Filter Berdasarkan Tipe Pembayaran (QRIS / Tunai)
        if (paymentFilter !== 'all') {
            filteredData = filteredData.filter(trx => {
                // Kalau payment_method kosong, asumsikan Tunai
                const method = (trx.payment_method || 'Tunai').toLowerCase();
                return method === paymentFilter;
            });
        }

        // 3. Render Ulang Layar (Metrik + Tabel)
        renderDashboard(filteredData);
    }

    // Fungsi untuk menggambar data ke layar
    function renderDashboard(transactions) {
        const tbody = document.getElementById('sales-tbody');
        
        // Hitung ulang Total Revenue & Avg Order berdasarkan data yang sudah difilter
        const totalRev = transactions.reduce((sum, trx) => sum + parseFloat(trx.total_price), 0);
        const avgOrder = transactions.length > 0 ? totalRev / transactions.length : 0;
        
        document.getElementById('val-total-revenue').innerText = formatRupiah(totalRev);
        document.getElementById('val-avg-order').innerText = formatRupiah(avgOrder);

        // Kosongkan tabel
        tbody.innerHTML = '';

        if (transactions.length > 0) {
            transactions.forEach(trx => {
                const dateObj = new Date(trx.created_at);
                const dateString = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                
                const customerName = trx.customer_name || 'Pelanggan Walk-in';
                const paymentMethod = trx.payment_method || 'Tunai';
                const badgeColor = paymentMethod.toLowerCase() === 'tunai' ? 'background: #fff3cd; color: #856404;' : 'background: #fdfdfa; color: #555; border: 1px solid #ddd;';

                const actionBtn = `
                    <a href="/karyawan/pos/print/${trx.order_id}" target="_blank" style="color: #4c7c5f; text-decoration: none; font-size: 16px;" title="Lihat Struk">
                        👁️
                    </a>
                `;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">${dateString}</td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; color: #888;">#EMB-${trx.order_id}</td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;"><strong>${customerName}</strong></td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;"><strong>${formatRupiah(trx.total_price)}</strong></td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; ${badgeColor}">${paymentMethod}</span>
                    </td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; text-align: center;">${actionBtn}</td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">Tidak ada data transaksi yang cocok dengan filter.</td></tr>';
        }
    }
</script>
@endsection
