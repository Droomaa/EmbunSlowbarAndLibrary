@extends('layouts.karyawan')

@section('title', 'Embun Cafe Operations')

@section('content')
    <div style="background: #4c7c5f; color: white; padding: 30px; border-radius: 12px; margin-bottom: 25px;">
        <h2 id="greet-name" style="margin: 0 0 10px 0;">Semangat Pagi, ...!</h2>
        <p id="greet-desc" style="margin: 0 0 20px 0; opacity: 0.9; font-size: 14px;">Memuat data operasional hari ini...</p>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.location.href='/karyawan/stok'" style="background: #fdf5d3; color: #856404; border: none; padding: 8px 20px; border-radius: 6px; font-weight: bold; cursor: pointer;">Lihat Laporan Stok</button>
            <button style="background: transparent; color: white; border: 1px solid rgba(255,255,255,0.5); padding: 8px 20px; border-radius: 6px; cursor: pointer;">Kelola Shift</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div class="card" style="display: flex; gap: 15px; align-items: center;">
            <div style="background: #e6f4ea; padding: 15px; border-radius: 8px; font-size: 20px;">🛍️</div>
            <div>
                <p style="margin: 0; font-size: 12px; color: #666;">Incoming Orders</p>
                <h2 id="val-incoming" style="margin: 5px 0;">0</h2>
                <p style="margin: 0; font-size: 11px; color: #1e8e3e;">Pesanan Menunggu</p>
            </div>
        </div>
        <div class="card" style="display: flex; gap: 15px; align-items: center;">
            <div style="background: #fdf5d3; padding: 15px; border-radius: 8px; font-size: 20px;">📅</div>
            <div>
                <p style="margin: 0; font-size: 12px; color: #666;">Today's Reservations</p>
                <h2 id="val-reservations" style="margin: 5px 0;">0</h2>
                <p style="margin: 0; font-size: 11px; color: #856404;">Reservasi Hari Ini</p>
            </div>
        </div>
        <div class="card" style="display: flex; gap: 15px; align-items: center; border: 1px solid #fce8e6; background: #fffaf9;">
            <div style="background: #fce8e6; color: #dc3545; padding: 15px; border-radius: 8px; font-size: 20px;">⚠️</div>
            <div>
                <p style="margin: 0; font-size: 12px; color: #dc3545; font-weight: bold;">Low Stock Alerts</p>
                <h2 id="val-lowstock" style="margin: 5px 0; color: #dc3545;">00</h2>
                <p style="margin: 0; font-size: 11px; color: #666;">Butuh Perhatian</p>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <h4 style="margin: 0;">🛒 Pesanan Masuk (Terbaru)</h4>
                <a href="/karyawan/online" style="font-size: 12px; color: #4c7c5f; text-decoration: none; font-weight: bold;">Lihat Semua</a>
            </div>
            
            <div id="online-orders-container">
                <p style="text-align: center; font-size: 13px; color: #888; padding: 20px 0;">⏳ Memuat pesanan...</p>
            </div>
        </div>

        <div class="card">
            <h4 style="margin: 0 0 15px 0;">📅 Verifikasi Reservasi</h4>
            
            <div id="reservations-container">
                <p style="text-align: center; font-size: 13px; color: #888; padding: 20px 0;">⏳ Memuat reservasi...</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    if (typeof API_URL === 'undefined') {
        var API_URL = 'http://127.0.0.1:8000/api';
    }
    const token = localStorage.getItem('embun_token');

    document.addEventListener('DOMContentLoaded', async () => {
        const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);

        try {
            const response = await fetch(`${API_URL}/karyawan/dashboard/data`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await response.json();
            
            if (response.ok) {
                // UPDATE NAMA & METRIK
                document.getElementById('greet-name').innerText = `Semangat Pagi, ${result.user.name}!`;
                document.getElementById('greet-desc').innerText = `Hari ini ada ${result.metrics.today_reservations} reservasi terdaftar dan ${result.metrics.low_stock} stok bahan yang hampir habis. Mari berikan pelayanan terbaik untuk pelanggan Embun Cafe.`;
                
                if(document.getElementById('layout-user-name')) document.getElementById('layout-user-name').innerText = result.user.name;
                if(document.getElementById('layout-user-role')) document.getElementById('layout-user-role').innerText = result.user.role;
                if(document.getElementById('layout-user-avatar')) document.getElementById('layout-user-avatar').innerText = result.user.initial;

                document.getElementById('val-incoming').innerText = result.metrics.pending_orders || 0;
                document.getElementById('val-reservations').innerText = result.metrics.today_reservations || 0;
                document.getElementById('val-lowstock').innerText = (result.metrics.low_stock < 10 ? '0' : '') + (result.metrics.low_stock || 0);

                // UPDATE PESANAN ONLINE
                const ordersContainer = document.getElementById('online-orders-container');
                ordersContainer.innerHTML = '';
                if (result.online_orders && result.online_orders.length > 0) {
                    result.online_orders.forEach(order => {
                        ordersContainer.innerHTML += `
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
                                <div style="display: flex; gap: 15px;">
                                    <div style="width: 50px; height: 50px; background: #e6f4ea; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">🛍️</div>
                                    <div>
                                        <strong style="display: block; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${order.items_text}</strong>
                                        <span style="font-size: 12px; color: #888;">Order #EB-${order.order_id} • Sistem Kasir/QR</span>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <strong style="display: block; margin-bottom: 5px;">${formatRupiah(order.total_price)}</strong>
                                    <span class="badge" style="background: #e6f4ea; color: #4c7c5f;">MENUNGGU</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    ordersContainer.innerHTML = '<p style="text-align: center; font-size: 13px; color: #888; padding: 20px 0;">Tidak ada pesanan tertunda.</p>';
                }

                // UPDATE RESERVASI
                const resContainer = document.getElementById('reservations-container');
                resContainer.innerHTML = '';
                if (result.reservations && result.reservations.length > 0) {
                    result.reservations.forEach(res => {
                        // Ekstrak Jam dari DATETIME reservation_date
                        let timeString = '18:00';
                        if (res.reservation_date) {
                            const dateObj = new Date(res.reservation_date);
                            timeString = `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        }

                        // Cek status (Pending vs Confirmed) dari database
                        const isPending = res.status.toLowerCase() === 'pending';
                        
                        const actionHtml = isPending
                            ? `<button class="btn-primary" onclick="window.location.href='/karyawan/reservasi'" style="flex: 1; padding: 6px;">Verifikasi</button>`
                            : `<div style="flex: 1; text-align: center; color: #1e8e3e; font-size: 12px; font-weight: bold; background: #e6f4ea; padding: 6px; border-radius: 6px;">✔️ Confirmed</div>`;

                        resContainer.innerHTML += `
                            <div class="searchable-item" style="background: #f4f3ed; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <div>
                                        <strong style="display: block;">${res.customer_name || 'Tamu'}</strong>
                                        <span style="font-size: 11px; color: #666;">${res.pax || 2} Tamu • Hari ini, ${timeString} WIB</span>
                                    </div>
                                    <span style="background: #e2e8e4; font-size: 10px; padding: 2px 6px; border-radius: 4px; height: fit-content; color: #4c7c5f; font-weight: bold;">MEJA ${res.table_number || '?'}</span>
                                </div>
                                <div style="display: flex; gap: 5px;">
                                    ${actionHtml}
                                </div>
                            </div>
                        `;
                    });
                } else {
                    resContainer.innerHTML = '<p style="text-align: center; font-size: 13px; color: #888; padding: 20px 0;">Tidak ada reservasi hari ini.</p>';
                }
            }
        } catch (error) {
            console.error("Gagal memuat data dashboard:", error);
            document.getElementById('greet-desc').innerText = "Gagal memuat data dari server. Periksa koneksi Anda.";
        }
    });
</script>
@endsection
