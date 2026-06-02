@extends('layouts.karyawan')

@section('title', 'Pesanan Online Masuk')

@section('content')
    <div style="background: #e6f4ea; color: #1e8e3e; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 15px; align-items: center;">
            <span style="font-size: 24px;" id="refresh-icon">✨</span>
            <div>
                <strong style="display: block; font-size: 18px;">Monitor Pesanan Real-time</strong>
                <span style="font-size: 13px;">Antrian pesanan online (Delivery/Pick Up) sedang aktif.</span>
            </div>
        </div>
        <div style="text-align: right;">
            <h2 id="val-total-orders" style="margin: 0;">0</h2>
            <span style="font-size: 11px; font-weight: bold; text-transform: uppercase;">Total Pesanan Online</span>
        </div>
    </div>

    <div id="kanban-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        
        <div id="performa-card" class="card" style="background: #2e5a40; color: white;">
            <h3 style="margin: 0 0 10px 0;">Performa Shift</h3>
            <p style="font-size: 13px; opacity: 0.9; line-height: 1.5; margin-bottom: 20px;">Anda telah melayani 85% pesanan tepat waktu hari ini.</p>
            
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <div style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 6px; flex: 1;">
                    <span style="font-size: 10px; opacity: 0.8; display: block; margin-bottom: 5px;">AVG PREP</span>
                    <strong style="font-size: 20px;">8.5m</strong>
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 6px; flex: 1;">
                    <span style="font-size: 10px; opacity: 0.8; display: block; margin-bottom: 5px;">CANCELED</span>
                    <strong style="font-size: 20px;">0</strong>
                </div>
            </div>
        </div>
        
    </div>
@endsection

@section('scripts')
<script>
    if (typeof API_URL === 'undefined') {
        var API_URL = 'http://127.0.0.1:8000/api';
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        loadOnlineOrders();
        
        setInterval(() => {
            const icon = document.getElementById('refresh-icon');
            icon.innerText = '🔄'; 
            loadOnlineOrders().finally(() => {
                setTimeout(() => icon.innerText = '✨', 500); 
            });
        }, 10000); 
    });

    async function loadOnlineOrders() {
        const token = localStorage.getItem('embun_token');
        const container = document.getElementById('kanban-container');
        const performaCard = document.getElementById('performa-card'); 

        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        };

        try {
            const response = await fetch(`${API_URL}/karyawan/orders/online`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (response.ok) {
                let allOrders = result.data || [];
                
                // 🌟 FILTER KHUSUS ONLINE (Hanya Delivery / Pick Up)
                const onlineOrders = allOrders.filter(trx => {
                    const type = (trx.order_type || '').toLowerCase();
                    return type.includes('delivery') || type.includes('pick');
                });

                document.getElementById('val-total-orders').innerText = onlineOrders.length;

                container.innerHTML = ''; 
                
                if (onlineOrders.length > 0) {
                    onlineOrders.reverse().forEach(trx => {
                        const dateObj = new Date(trx.created_at);
                        const timeString = `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        
                        let itemsHtml = '';
                        if (trx.items && trx.items.length > 0) {
                            trx.items.forEach(item => {
                                const menuName = item.menuName || 'Menu';
                                itemsHtml += `
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; border-bottom: 1px dashed #ddd; padding-bottom: 5px;">
                                        <span style="font-weight: bold; color: #4c7c5f;">${item.quantity}x <span style="color: #555;">${menuName}</span></span>
                                        <strong>${formatRupiah(item.subtotal)}</strong>
                                    </div>
                                `;
                            });
                        } else {
                            itemsHtml = '<div style="color: #888; font-style: italic;">Tidak ada item terdeteksi.</div>';
                        }

                        const orderType = trx.order_type || 'Delivery';
                        const borderColor = '#4c7c5f';
                        const typeLabel = orderType.toUpperCase();
                        
                        const card = document.createElement('div');
                        card.className = 'card searchable-item';
                        card.style.cssText = `border-top: 4px solid ${borderColor}; position: relative; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);`;
                        
                        card.innerHTML = `
                            <span style="position: absolute; top: 15px; right: 15px; background: #eee; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; color: #555;">${typeLabel}</span>
                            <p style="font-size: 11px; color: #888; margin: 0;">ORDER ID #EB-${trx.order_id}</p>
                            <h3 style="margin: 5px 0 15px 0; color: #333; line-height: 1.2;">${trx.customer_name}</h3>
                            
                            <div style="background: #fdfdfa; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; color: #333;">
                                ${itemsHtml}
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <span style="color: #888; font-size: 12px;">TOTAL</span><strong style="color: #4c7c5f; font-size: 18px;">${formatRupiah(trx.total_price)}</strong>
                            </div>
                            <p style="font-size: 11px; color: #888; margin-bottom: 15px;">🕒 Masuk pukul ${timeString} WIB</p>
                            
                            <button onclick="ubahStatus(${trx.order_id}, 'Completed')" style="width: 100%; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.2s; background: #4c7c5f; color: white; border: none;">✔️ Siap Dikirim</button>
                        `;

                        container.appendChild(card);
                    });
                } else {
                    container.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #888;">✨ Tidak ada antrean pesanan online saat ini.</div>';
                }
                
                container.appendChild(performaCard);
            }
        } catch (error) {
            console.error("Gagal memuat pesanan online:", error);
        }
    }

    window.ubahStatus = async function(id, newStatus) {
        if(!confirm(`Yakin pesanan ini sudah selesai dan siap dikirim?`)) return;

        const token = localStorage.getItem('embun_token');
        try {
            const response = await fetch(`${API_URL}/karyawan/orders/${id}/status`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            });

            if(response.ok) {
                loadOnlineOrders();
            } else {
                const errData = await response.json();
                alert(`Gagal: ${errData.error || errData.message || 'Rute API tidak ditemukan'}`);
            }
        } catch (error) {
            alert("Terjadi kesalahan koneksi ke server.");
        }
    }
</script>
@endsection
