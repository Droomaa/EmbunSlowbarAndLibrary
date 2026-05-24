@extends('layouts.owner')

@section('title', 'Transaction Management')

@section('content')
    <style>
        .filter-select { padding: 10px 15px; border-radius: 6px; border: 1px solid #ddd; outline: none; font-size: 13px; cursor: pointer; background: white; }
        .action-icon { background: none; border: none; font-size: 16px; cursor: pointer; margin: 0 5px; opacity: 0.7; transition: 0.2s; }
        .action-icon:hover { opacity: 1; transform: scale(1.1); }
        
        /* Modal Style */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 25px; border-radius: 12px; width: 100%; max-width: 350px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h3 style="margin: 0; color: #333;">Transaction History</h3>
            <p style="margin: 5px 0 0 0; color: #888; font-size: 13px;">Review and manage all customer activities within the cafe ecosystem.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="/karyawan/pos" style="background: #2e5a40; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                + Add Transaction
            </a>
            <a href="/owner/transactions/export" style="background: white; color: #333; border: 1px solid #ddd; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                📥 Export CSV
            </a>
        </div>
    </div>

    <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
        
        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <select id="filter-date" class="filter-select">
                <option value="all">📅 All Time</option>
                <option value="today">📅 Today</option>
                <option value="7days" selected>📅 Last 7 Days</option>
            </select>

            <select id="filter-type" class="filter-select">
                <option value="all">All Types</option>
                <option value="Dine In">Dine-in</option>
                <option value="Takeaway">Takeaway</option>
                <option value="Delivery">Delivery</option>
            </select>
        </div>

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; font-size: 11px; color: #888; text-transform: uppercase;">
                    <th style="padding: 15px 5px;">Transaction ID</th>
                    <th style="padding: 15px 5px;">Date</th>
                    <th style="padding: 15px 5px;">Customer Name</th>
                    <th style="padding: 15px 5px;">Order Type</th>
                    <th style="padding: 15px 5px;">Total Amount</th>
                    <th style="padding: 15px 5px;">Payment</th>
                    <th style="padding: 15px 5px;">Status</th>
                    <th style="padding: 15px 5px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody id="trx-tbody" style="font-size: 13px; color: #555;">
                <tr><td colspan="8" style="text-align:center; padding: 20px;">⏳ Memuat data...</td></tr>
            </tbody>
        </table>
    </div>

    <div id="modal-status" class="modal-overlay">
        <div class="modal-content">
            <h3 style="margin-top: 0;">Update Status</h3>
            <p style="font-size: 12px; color: #888; margin-bottom: 15px;">Ubah status untuk pesanan <strong id="modal-trx-id"></strong></p>
            
            <input type="hidden" id="input-trx-id">
            <select id="input-status" class="filter-select" style="width: 100%; margin-bottom: 20px;">
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Reject">Reject</option>
            </select>

            <div style="display: flex; gap: 10px;">
                <button onclick="closeModal()" style="flex: 1; padding: 10px; border-radius: 6px; border: 1px solid #ddd; background: white; cursor: pointer;">Batal</button>
                <button onclick="saveStatus()" style="flex: 1; padding: 10px; border-radius: 6px; border: none; background: #2e5a40; color: white; font-weight: bold; cursor: pointer;">Simpan</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const API_URL_TRX = 'http://127.0.0.1:8000/api';
    const token = localStorage.getItem('embun_token');
    
    let allTransactions = [];

    document.addEventListener('DOMContentLoaded', () => {
        loadTransactions();

        // Event Listeners untuk Dropdown Filter
        document.getElementById('filter-date').addEventListener('change', applyFilters);
        document.getElementById('filter-type').addEventListener('change', applyFilters);
    });

    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    };

    const getInitials = (name) => {
        if(!name) return 'Walk-in'.substring(0,2).toUpperCase();
        const words = name.trim().split(' ');
        if(words.length === 1) return words[0].substring(0,2).toUpperCase();
        return (words[0][0] + words[1][0]).toUpperCase();
    };

    async function loadTransactions() {
        try {
            const response = await fetch(`${API_URL_TRX}/owner/transactions/data`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await response.json();

            if (response.ok) {
                allTransactions = result.transactions || [];
                applyFilters(); // Terapkan filter awal
            }
        } catch (error) {
            console.error("Error:", error);
            document.getElementById('trx-tbody').innerHTML = `<tr><td colspan="8" style="text-align: center; color: red;">Gagal memuat data.</td></tr>`;
        }
    }

    function applyFilters() {
        const dateFilter = document.getElementById('filter-date').value;
        const typeFilter = document.getElementById('filter-type').value;

        let filtered = allTransactions;

        // 1. Filter Tanggal
        const today = new Date();
        if (dateFilter === 'today') {
            filtered = filtered.filter(trx => new Date(trx.created_at).toDateString() === today.toDateString());
        } else if (dateFilter === '7days') {
            const sevenDaysAgo = new Date();
            sevenDaysAgo.setDate(today.getDate() - 7);
            filtered = filtered.filter(trx => new Date(trx.created_at) >= sevenDaysAgo);
        }

        // 2. Filter Tipe Order
        if (typeFilter !== 'all') {
            filtered = filtered.filter(trx => (trx.order_type || '') === typeFilter);
        }

        renderTable(filtered);
    }

    function renderTable(data) {
        const tbody = document.getElementById('trx-tbody');
        tbody.innerHTML = '';

        if (data.length > 0) {
            data.forEach(trx => {
                const dateObj = new Date(trx.created_at);
                const dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const timeStr = dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                const custName = trx.customer_name || 'Walk-in Customer';
                const initials = getInitials(custName);
                
                const status = trx.status || 'Pending';
                let statusStyle = 'background: #fff3cd; color: #856404;';
                if(status === 'Completed') statusStyle = 'background: #e6f4ea; color: #1e8e3e;';
                if(status === 'Reject') statusStyle = 'background: #fff5f5; color: #e74c3c; border: 1px solid #ffe3e3;';

                const payMethod = trx.payment_method || '-';
                const payStyle = payMethod !== '-' ? 'background: #e6f4ea; color: #1e8e3e;' : 'background: #fdfdfa; color: #888; border: 1px solid #ddd;';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; color: #2e5a40; font-weight: bold;">#EMB-${trx.order_id}</td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">
                        ${dateStr} <br> <span style="color: #888; font-size: 11px;">${timeStr}</span>
                    </td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; color: #555;">${initials}</div>
                            <strong>${custName}</strong>
                        </div>
                    </td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; color: #555;">${trx.order_type || '-'}</td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;"><strong>${formatRupiah(trx.total_price)}</strong></td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; ${payStyle}">${payMethod}</span>
                    </td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; ${statusStyle}">${status}</span>
                    </td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; text-align: center;">
                        <a href="/karyawan/pos/print/${trx.order_id}" target="_blank" class="action-icon" title="View Receipt">👁️</a>
                        <button class="action-icon" onclick="openModal(${trx.order_id}, '${status}')" title="Edit Status">✏️</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #888; padding: 30px;">Tidak ada transaksi yang cocok dengan filter.</td></tr>`;
        }
    }

    window.openModal = function(id, currentStatus) {
        document.getElementById('input-trx-id').value = id;
        document.getElementById('modal-trx-id').innerText = `#EMB-${id}`;
        document.getElementById('input-status').value = currentStatus;
        document.getElementById('modal-status').style.display = 'flex';
    }

    window.closeModal = function() {
        document.getElementById('modal-status').style.display = 'none';
    }

    window.saveStatus = async function() {
        const id = document.getElementById('input-trx-id').value;
        const status = document.getElementById('input-status').value;

        try {
            const response = await fetch(`${API_URL_TRX}/owner/transactions/${id}/status`, {
                method: 'PUT',
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ status: status })
            });

            if (response.ok) {
                // 👇 TAMBAHKAN DUA BARIS INI UNTUK MUNCULIN ALERT 👇
                const dataRes = await response.json();
                alert(dataRes.message);
                
                closeModal();
                loadTransactions();
            } else {
                const errData = await response.json();
                alert('ERROR: ' + (errData.error || 'Gagal mengubah status.'));
            }
        } catch (error) {
            alert('Kesalahan jaringan.');
        }
    }
</script>
@endsection
