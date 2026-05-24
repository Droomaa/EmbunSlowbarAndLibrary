@extends('layouts.owner')

@section('title', 'Dashboard Overview')

@section('content')
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px;">
        
        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: start; position: relative;">
            <div>
                <span style="font-size: 12px; color: #888; font-weight: bold;">Today's Sales</span>
                <h2 id="val-today-sales" style="margin: 10px 0 0 0; font-size: 24px; color: #333;">Rp 0</h2>
            </div>
            <span style="font-size: 11px; color: #27ae60; font-weight: bold; background: #e6f4ea; padding: 3px 8px; border-radius: 20px; position: absolute; top: 20px; right: 20px;">📈 +12%</span>
        </div>

        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: start; position: relative;">
            <div>
                <span style="font-size: 12px; color: #888; font-weight: bold;">Total Orders</span>
                <h2 id="val-total-orders" style="margin: 10px 0 0 0; font-size: 24px; color: #333;">0</h2>
            </div>
            <span style="font-size: 11px; color: #27ae60; font-weight: bold; background: #e6f4ea; padding: 3px 8px; border-radius: 20px; position: absolute; top: 20px; right: 20px;">📈 +5%</span>
        </div>

        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: start; border-left: 4px solid #e74c3c;">
            <div>
                <span style="font-size: 12px; color: #888; font-weight: bold;">Low Stock Alerts</span>
                <h2 id="val-low-stock" style="margin: 10px 0 0 0; font-size: 24px; color: #e74c3c;">0 Items</h2>
            </div>
            <span style="font-size: 11px; color: #e74c3c; font-weight: bold; padding-top: 2px;">Action Needed</span>
        </div>

        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: start; border-left: 4px solid #3498db;">
            <div>
                <span style="font-size: 12px; color: #888; font-weight: bold;">Active Reservations</span>
                <h2 id="val-active-res" style="margin: 10px 0 0 0; font-size: 24px; color: #3498db;">0 Tables</h2>
            </div>
            <span style="font-size: 11px; color: #888; padding-top: 2px;">Next: 14:00</span>
        </div>

    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 25px;">
        
        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); min-height: 280px; display: flex; flex-direction: column;">
            <span style="font-size: 14px; font-weight: bold; color: #333; margin-bottom: 15px;">Weekly Sales Trends</span>
            <div style="flex: 1; background: #f9f9f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #888; font-style: italic; border: 1px dashed #ddd;">
                [ Area Render Grafik Bar Chart (Frontend Nanti) ]
            </div>
        </div>

        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
            <span style="font-size: 14px; font-weight: bold; color: #333; margin-bottom: 20px;">Stock Status</span>
            
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="width: 12px; height: 12px; background: #27ae60; border-radius: 50%; display: inline-block;"></span>
                        <span style="font-size: 14px; color: #555;">Available</span>
                    </div>
                    <strong style="font-size: 16px; color: #333;"><span id="val-stock-avail">0</span> items</strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="width: 12px; height: 12px; background: #f39c12; border-radius: 50%; display: inline-block;"></span>
                        <span style="font-size: 14px; color: #555;">Running Low</span>
                    </div>
                    <strong style="font-size: 16px; color: #333;"><span id="val-stock-low">0</span> items</strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="width: 12px; height: 12px; background: #e74c3c; border-radius: 50%; display: inline-block;"></span>
                        <span style="font-size: 14px; color: #555;">Out of Stock</span>
                    </div>
                    <strong style="font-size: 16px; color: #333;"><span id="val-stock-out">0</span> items</strong>
                </div>
            </div>
        </div>

    </div>

    <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
        <span style="font-size: 14px; font-weight: bold; color: #333; display: block; margin-bottom: 15px;">Recent Activities</span>
        
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; font-size: 12px; color: #888;">
                    <th style="padding: 10px 5px;">USER</th>
                    <th style="padding: 10px 5px;">ACTION</th>
                    <th style="padding: 10px 5px;">STATUS</th>
                    <th style="padding: 10px 5px;">TIMESTAMP</th>
                </tr>
            </thead>
            <tbody id="activities-table" style="font-size: 13px; color: #555;">
                <tr style="border-bottom: 1px solid #f9f9f9;">
                    <td style="padding: 15px 5px;"><strong>Budi Santoso</strong><br><span style="font-size:11px; color:#999;">Manager</span></td>
                    <td style="padding: 15px 5px;">Updated Stock: Coffee Beans (Arabica)</td>
                    <td style="padding: 15px 5px;"><span style="background: #e6f4ea; color: #1e8e3e; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold;">SUCCESS</span></td>
                    <td style="padding: 15px 5px; color: #999;">10 mins ago</td>
                </tr>
                <tr style="border-bottom: 1px solid #f9f9f9;">
                    <td style="padding: 15px 5px;"><strong>Sandro Mahesa</strong><br><span style="font-size:11px; color:#999;">Developer</span></td>
                    <td style="padding: 15px 5px;">Compiled Dashboard Widgets</td>
                    <td style="padding: 15px 5px;"><span style="background: #e6f4ea; color: #1e8e3e; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold;">SUCCESS</span></td>
                    <td style="padding: 15px 5px; color: #999;">1 hr ago</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    const API_URL_OWNER = 'http://127.0.0.1:8000/api';
    const token_owner = localStorage.getItem('embun_token');

    document.addEventListener('DOMContentLoaded', () => {
        loadDashboardData();
    });

    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    };

    async function loadDashboardData() {
        try {
            const response = await fetch(`${API_URL_OWNER}/owner/dashboard/data`, {
                headers: {
                    'Authorization': `Bearer ${token_owner}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok) {
                document.getElementById('val-today-sales').innerText = formatRupiah(result.today_sales);
                document.getElementById('val-total-orders').innerText = result.total_orders;
                document.getElementById('val-low-stock').innerText = `${result.low_stock_alerts} Items`;
                document.getElementById('val-active-res').innerText = `${result.active_reservations} Tables`;

                document.getElementById('val-stock-avail').innerText = result.stock_status.available;
                document.getElementById('val-stock-low').innerText = result.stock_status.running_low;
                document.getElementById('val-stock-out').innerText = result.stock_status.out_of_stock;
            }
        } catch (error) {
            console.error("Gagal memuat data dashboard owner:", error);
        }
    }
</script>
@endsection
