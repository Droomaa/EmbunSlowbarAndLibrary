@extends('layouts.karyawan')

@section('title', 'Verification Queue')

@section('content')
    <p style="color: #666; margin-top: -15px; margin-bottom: 25px;">Review and approve customer table bookings for today and tomorrow.</p>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px;">
        <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
            <div><p style="color: #888; font-size: 11px; margin: 0;">PENDING</p><h2 id="val-res-pending" style="margin: 5px 0 0 0; color: #856404;">0</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">⏳</span>
        </div>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
            <div><p style="color: #888; font-size: 11px; margin: 0;">CONFIRMED</p><h2 id="val-res-confirmed" style="margin: 5px 0 0 0; color: #1e8e3e;">0</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">✅</span>
        </div>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
            <div><p style="color: #888; font-size: 11px; margin: 0;">AVAILABLE TABLES</p><h2 id="val-res-tables" style="margin: 5px 0 0 0;">0</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">🪑</span>
        </div>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
            <div><p style="color: #888; font-size: 11px; margin: 0;">WAITLIST</p><h2 id="val-res-wait" style="margin: 5px 0 0 0;">0</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">👥</span>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 15px; gap: 10px;">
            <input type="date" style="padding: 8px; border: 1px solid #ddd; border-radius: 20px;">
            <button class="btn-primary">⚙️ Filter</button>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">CUSTOMER NAME</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">DATE & TIME</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">GUESTS</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">NOTES</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">STATUS</th>
                </tr>
            </thead>
            <tbody id="res-tbody">
                </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('embun_token');
        const tbody = document.getElementById('res-tbody');

        try {
            const response = await fetch(`${API_URL}/karyawan/reservations`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (response.ok) {
                const data = result.data;

                // 1. Update Metrik Atas
                document.getElementById('val-res-pending').innerText = data.pending < 10 ? `0${data.pending}` : data.pending;
                document.getElementById('val-res-confirmed').innerText = data.confirmed < 10 ? `0${data.confirmed}` : data.confirmed;
                document.getElementById('val-res-tables').innerText = data.available_tables < 10 ? `0${data.available_tables}` : data.available_tables;
                document.getElementById('val-res-wait').innerText = data.waitlist < 10 ? `0${data.waitlist}` : data.waitlist;

                // 2. Render Tabel Reservasi
                if (data.reservations && data.reservations.length > 0) {
                    tbody.innerHTML = '';
                    
                    data.reservations.forEach(res => {
                        // FIX: Menggunakan reservation_date sesuai database kamu
                        const dateObj = new Date(res.reservation_date);
                        const dateString = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        const timeString = `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        
                        // Buat inisial nama
                        const initials = res.customer_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

                        // FIX: Deteksi status (kebal huruf besar/kecil dari database)
                        let statusLower = res.status ? res.status.toLowerCase() : '';
                        let statusBadge = '';
                        if (statusLower === 'pending') statusBadge = '<span class="badge badge-warning">PENDING</span>';
                        else if (statusLower === 'confirmed') statusBadge = '<span class="badge badge-success" style="background: #e6f4ea; color: #1e8e3e;">CONFIRMED</span>';
                        else statusBadge = `<span class="badge" style="background: #eee; color: #555;">${res.status ? res.status.toUpperCase() : ''}</span>`;

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee; display: flex; gap: 10px; align-items: center;">
                                <span style="background: #e6f4ea; color: #1e8e3e; padding: 10px; border-radius: 50%; font-size: 12px; font-weight: bold; width: 16px; height: 16px; display: flex; justify-content: center; align-items: center;">${initials}</span>
                                <div><strong>${res.customer_name}</strong><br><span style="font-size: 11px; color: #888;">${res.phone_number || '-'}</span></div>
                            </td>
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee; font-size: 13px;">${dateString}<br><strong>${timeString} WIB</strong></td>
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee; font-size: 13px;">👥 ${res.pax} People</td>
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee; font-size: 13px; color: #666;">${res.notes || '-'}</td>
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee;">${statusBadge}</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px; color: #888;">Tidak ada antrean reservasi.</td></tr>';
                }
            }
        } catch (error) {
            console.error("Gagal memuat reservasi:", error);
        }
    });
</script>
@endsection
