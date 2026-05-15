@extends('layouts.owner')

@section('title', 'Dashboard Overview')

@section('content')
    <div class="grid-4">
        <div class="card">
            <p style="color: #888; font-size: 12px; margin: 0;">Today's Sales <span class="text-green" style="float: right;">↗ +12%</span></p>
            <h2 style="margin: 10px 0 0 0;" id="val-sales">Rp 0</h2>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 12px; margin: 0;">Total Orders <span class="text-green" style="float: right;">↗ +5%</span></p>
            <h2 style="margin: 10px 0 0 0;" id="val-orders">0</h2>
        </div>
        <div class="card" style="border-left: 4px solid #dc3545;">
            <p style="color: #888; font-size: 12px; margin: 0; color: #dc3545;">Low Stock Alerts <span style="float: right; font-weight: bold;">Action Needed</span></p>
            <h2 style="margin: 10px 0 0 0;" id="val-lowstock">0 Items</h2>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 12px; margin: 0;">Active Reservations <span style="float: right;">Next: 14:00</span></p>
            <h2 style="margin: 10px 0 0 0;" id="val-reservations">0 Tables</h2>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 20px;">
        <div class="card">
            <h4>Weekly Sales Trends</h4>
            <div style="height: 200px; background-color: #f4f4f4; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #888;">
                [ Area Render Grafik Bar Chart (Frontend Nanti) ]
            </div>
        </div>
        <div class="card">
            <h4>Stock Status</h4>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 10px;">🟢 Available <strong style="float: right;">50 items</strong></li>
                <li style="margin-bottom: 10px;">🟡 Running Low <strong style="float: right;">4 items</strong></li>
                <li style="margin-bottom: 10px;">🔴 Out of Stock <strong style="float: right;">2 items</strong></li>
            </ul>
        </div>
    </div>

    <div class="card">
        <h4 style="margin-top: 0;">Recent Activities</h4>
        <table id="activity-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Budi Santoso</strong><br><small>Manager</small></td>
                    <td>Updated Stock: Coffee Beans (Arabica)</td>
                    <td><span class="badge badge-success">SUCCESS</span></td>
                    <td style="color: #888; font-size: 12px;">10 mins ago</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    // Di sini nanti tempat kamu naruh fetch() API khusus untuk memuat data Dashboard
    console.log("Halaman Dashboard siap!");
</script>
@endsection
