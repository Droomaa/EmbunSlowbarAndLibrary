<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Offline - Embun Cafe</title>
    <style>
        body { font-family: sans-serif; background: #f4f3ed; margin: 0; padding: 20px; color: #333; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h2 { color: #4c7c5f; margin: 0; }
        .btn-nav { text-decoration: none; background: white; padding: 8px 15px; border-radius: 8px; color: #555; font-weight: bold; border: 1px solid #ddd; }
        
        .board { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .order-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-left: 5px solid #4c7c5f; }
        .order-card.Pending { border-left-color: #f39c12; }
        .order-card.Completed { border-left-color: #27ae60; opacity: 0.7; }
        .order-card.Reject { border-left-color: #e74c3c; opacity: 0.5; }
        
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; background: #eee; }
        .badge.dinein { background: #e6f4ea; color: #1e8e3e; }
        .badge.takeaway { background: #fce8e6; color: #d93025; }
        
        .action-btns { display: flex; gap: 10px; margin-top: 15px; }
        .btn { flex: 1; padding: 8px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; color: white; }
        .btn-acc { background: #27ae60; }
        .btn-rej { background: #e74c3c; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <h2>🍽️ Pesanan Masuk (Dine In & Takeaway)</h2>
            <p style="margin: 5px 0 0 0; color: #666;">Kelola antrean khusus makan di tempat & bawa pulang</p>
        </div>
        <a href="/karyawan/dashboard" class="btn-nav">⬅️ Kembali ke Dashboard</a>
    </div>

    <div class="board" id="order-board">
        <p style="color: #888;">⏳ Memuat data pesanan...</p>
    </div>

    <script>
        const API_URL = 'http://127.0.0.1:8000/api';

        document.addEventListener('DOMContentLoaded', loadOrders);

        async function loadOrders() {
            try {
                const res = await fetch(`${API_URL}/karyawan/orders/offline`);
                const result = await res.json();
                renderBoard(result.data);
            } catch (error) {
                document.getElementById('order-board').innerHTML = '<p style="color:red;">❌ Gagal memuat data server.</p>';
            }
        }

        function renderBoard(orders) {
            const board = document.getElementById('order-board');
            board.innerHTML = '';

            if(orders.length === 0) {
                board.innerHTML = '<p style="color: #888;">Belum ada pesanan offline masuk.</p>';
                return;
            }

            orders.forEach(order => {
                const typeClass = order.order_type === 'Dine In' ? 'dinein' : 'takeaway';
                const typeIcon = order.order_type === 'Dine In' ? '🍽️' : '🥡';
                const time = new Date(order.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});

                board.innerHTML += `
                    <div class="order-card ${order.status}">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <strong style="font-size: 18px;">Meja ${order.table_number || '-'}</strong>
                            <span class="badge ${typeClass}">${typeIcon} ${order.order_type}</span>
                        </div>
                        <p style="margin: 0 0 5px 0; font-size: 14px; color: #555;">👤 ${order.customer_name}</p>
                        <p style="margin: 0 0 15px 0; font-size: 12px; color: #888;">🕒 ${time} | Status: <strong>${order.status}</strong></p>
                        
                        ${order.status === 'Pending' ? `
                            <div class="action-btns">
                                <button class="btn btn-acc" onclick="updateStatus(${order.order_id}, 'Completed')">✅ Selesai</button>
                                <button class="btn btn-rej" onclick="updateStatus(${order.order_id}, 'Reject')">❌ Tolak</button>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
        }

        async function updateStatus(id, newStatus) {
            if(!confirm(`Yakin mengubah pesanan ini menjadi ${newStatus}?`)) return;

            try {
                const res = await fetch(`${API_URL}/karyawan/orders/${id}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ status: newStatus })
                });

                if(res.ok) {
                    loadOrders(); // Refresh board otomatis
                } else {
                    alert("Gagal merubah status.");
                }
            } catch (error) {
                alert("Gagal terhubung ke server.");
            }
        }
    </script>
</body>
</html>
