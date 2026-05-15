@extends('layouts.karyawan')

@section('title', 'Pesanan Online Masuk')

@section('content')
    <div style="background: #e6f4ea; color: #1e8e3e; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 15px; align-items: center;">
            <span style="font-size: 24px;">✨</span>
            <div>
                <strong style="display: block; font-size: 18px;">Monitor Pesanan Real-time</strong>
                <span style="font-size: 13px;">Antrian pesanan sedang aktif. Pastikan waktu penyajian sesuai standar.</span>
            </div>
        </div>
        <div style="text-align: right;">
            <h2 id="val-total-orders" style="margin: 0;">0</h2>
            <span style="font-size: 11px; font-weight: bold; text-transform: uppercase;">Total Pesanan Hari Ini</span>
        </div>
    </div>

    <div id="kanban-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">

        <div class="card" style="background: #2e5a40; color: white;">
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
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('embun_token');
        const container = document.getElementById('kanban-container');

        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        };

        try {
            const response = await fetch(`${API_URL}/karyawan/online-orders`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (response.ok) {
                const data = result.data;

                document.getElementById('val-total-orders').innerText = data.total_pesanan || 0;

                if (data.pesanan && data.pesanan.length > 0) {
                    data.pesanan.slice().reverse().forEach(trx => {
                        const dateObj = new Date(trx.created_at);
                        const timeString = `${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}`;
                        
                        let itemsHtml = '';
                        if (trx.items && trx.items.length > 0) {
                            trx.items.forEach(item => {
                                const menuName = item.menu ? item.menu.menuName : 'Menu';
                                itemsHtml += `
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span>${item.quantity}x ${menuName}</span>
                                        <strong>${formatRupiah(item.subtotal)}</strong>
                                    </div>
                                `;
                            });
                        }

                        const isDelivery = Math.random() > 0.5;
                        const borderColor = isDelivery ? '#4c7c5f' : '#fdf5d3';
                        const typeLabel = isDelivery ? 'Delivery' : 'Pickup';
                        const btnStyle = isDelivery ? 'btn-primary' : 'btn-outline';
                        const btnText = isDelivery ? '✔️ Terima Pesanan' : '✔️ Selesaikan';

                        const card = document.createElement('div');
                        card.className = 'card';
                        card.style.cssText = `border-top: 4px solid ${borderColor}; position: relative;`;
                        
                        card.innerHTML = `
                            <span style="position: absolute; top: 15px; right: 15px; background: #eee; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold;">${typeLabel}</span>
                            <p style="font-size: 11px; color: #888; margin: 0;">ORDER ID #EB-${trx.order_id}</p>
                            <h3 style="margin: 5px 0 15px 0;">${trx.customer_name}</h3>
                            
                            <div style="background: #f9f9f9; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 13px;">
                                ${itemsHtml}
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <span style="color: #888; font-size: 12px;">TOTAL</span><strong style="color: #4c7c5f; font-size: 18px;">${formatRupiah(trx.total_price)}</strong>
                            </div>
                            <p style="font-size: 11px; color: #888; margin-bottom: 15px;">🕒 Masuk pukul ${timeString} WIB</p>
                            <button class="${btnStyle}" style="width: 100%;">${btnText}</button>
                        `;

                        // Masukkan kartu ke urutan paling depan (sebelum kartu performa)
                        container.insertBefore(card, container.firstChild);
                    });
                }
            }
        } catch (error) {
            console.error("Gagal memuat pesanan online:", error);
        }
    });
</script>
@endsection
