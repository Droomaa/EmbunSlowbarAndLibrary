@extends('layouts.karyawan')

@section('title', 'Verification Queue')

@section('content')
    <p style="color: #666; margin-top: -15px; margin-bottom: 25px;">Review and approve customer table bookings for today and tomorrow.</p>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px;">
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            <div><p style="color: #888; font-size: 11px; margin: 0; font-weight: bold;">PENDING</p><h2 id="val-res-pending" style="margin: 5px 0 0 0; color: #856404;">0</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">⏳</span>
        </div>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            <div><p style="color: #888; font-size: 11px; margin: 0; font-weight: bold;">CONFIRMED</p><h2 id="val-res-confirmed" style="margin: 5px 0 0 0; color: #1e8e3e;">0</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">✅</span>
        </div>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            <div><p style="color: #888; font-size: 11px; margin: 0; font-weight: bold;">AVAILABLE TABLES</p><h2 id="val-res-tables" style="margin: 5px 0 0 0;">0</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">🪑</span>
        </div>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            <div><p style="color: #888; font-size: 11px; margin: 0; font-weight: bold;">WAITLIST</p><h2 id="val-res-wait" style="margin: 5px 0 0 0;">0</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">👥</span>
        </div>
    </div>

    <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 15px; gap: 10px;">
            <input type="date" style="padding: 8px; border: 1px solid #ddd; border-radius: 20px; outline: none;">
            <button class="btn-primary" style="background: #4c7c5f; color: white; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer;">⚙️ Filter</button>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">CUSTOMER NAME</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">DATE & TIME</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">GUESTS</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">NOTES</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">STATUS</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: center; font-size: 11px; color: #888;">ACTION</th>
                </tr>
            </thead>
            <tbody id="res-tbody">
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    // Kita ganti namanya biar nggak bentrok sama layouts.karyawan
    const API_URL_RES = 'http://127.0.0.1:8000/api';
    const token_res = localStorage.getItem('embun_token');

    document.addEventListener('DOMContentLoaded', loadReservations);

    async function loadReservations() {
        const tbody = document.getElementById('res-tbody');

        try {
            // Pakai variabel yang baru
            const response = await fetch(`${API_URL_RES}/karyawan/reservations/data`, {
                headers: {
                    'Authorization': `Bearer ${token_res}`,
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
                        const dateObj = new Date(res.reservation_date);
                        const dateString = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        
                        // Fallback waktu
                        const timeString = res.reservation_time ? res.reservation_time : `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        
                        const initials = res.customer_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

                        let statusLower = res.status ? res.status.toLowerCase() : '';
                        let statusBadge = '';
                        let actionButtons = '-';

                        if (statusLower === 'pending') statusBadge = '<span class="badge" style="background: #fdf5e6; color: #856404; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: bold;">PENDING</span>';
                        else if (statusLower === 'confirmed') statusBadge = '<span class="badge" style="background: #e6f4ea; color: #1e8e3e; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: bold;">CONFIRMED</span>';
                        else statusBadge = `<span class="badge" style="background: #eee; color: #555; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: bold;">${res.status ? res.status.toUpperCase() : ''}</span>`;

                        const resId = res.id || res.reservation_id;
                        if (statusLower === 'pending') {
                            actionButtons = `
                                <button onclick="ubahStatus(${resId}, 'Confirmed')" style="background: #3498db; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: bold; margin-right: 5px;">Terima</button>
                                <button onclick="ubahStatus(${resId}, 'Cancelled')" style="background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: bold;">Tolak</button>
                            `;
                        } else if (statusLower === 'confirmed') {
                            actionButtons = `
                                <button onclick="ubahStatus(${resId}, 'Completed')" style="background: #27ae60; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: bold;">Selesaikan</button>
                            `;
                        }

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee; display: flex; gap: 10px; align-items: center;">
                                <span style="background: #e6f4ea; color: #1e8e3e; padding: 10px; border-radius: 50%; font-size: 12px; font-weight: bold; width: 16px; height: 16px; display: flex; justify-content: center; align-items: center;">${initials}</span>
                                <div><strong style="color:#333;">${res.customer_name}</strong><br><span style="font-size: 11px; color: #888;">${res.phone_number || '-'}</span></div>
                            </td>
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee; font-size: 13px; color:#555;">${dateString}<br><strong style="color:#333;">${timeString} WIB</strong></td>
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee; font-size: 13px; color:#555;">👥 ${res.pax || res.guest_count || 1} People</td>
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee; font-size: 13px; color: #888;">${res.notes || '-'}</td>
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee;">${statusBadge}</td>
                            <td style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: center;">${actionButtons}</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">✨ Tidak ada antrean reservasi.</td></tr>';
                }
            }
        } catch (error) {
            console.error("Gagal memuat reservasi:", error);
        }
    }

    async function ubahStatus(id, newStatus) {
        if(!confirm(`Yakin mengubah status reservasi ini menjadi ${newStatus}?`)) return;

        try {
            // Pakai variabel yang baru juga di sini
            const response = await fetch(`${API_URL_RES}/karyawan/reservations/${id}/status`, {
                method: 'POST',
                headers: { 
                    'Authorization': `Bearer ${token_res}`,
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ status: newStatus })
            });

            if(response.ok) {
                loadReservations(); 
            } else {
                alert("Gagal merubah status. Silakan coba lagi.");
            }
        } catch (error) {
            alert("Gagal terhubung ke server.");
            console.error(error);
        }
    }
</script>
@endsection

