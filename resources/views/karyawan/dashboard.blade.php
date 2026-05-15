@extends('layouts.karyawan')

@section('content')
    <div style="background: #4c7c5f; color: white; padding: 30px; border-radius: 12px; margin-bottom: 25px;">
        <h2 style="margin: 0 0 10px 0;">Semangat Pagi, Budi Santoso!</h2>
        <p style="margin: 0 0 20px 0; opacity: 0.9; font-size: 14px;">Hari ini ada 12 reservasi terdaftar dan 4 stok bahan yang hampir habis. Mari berikan pelayanan terbaik untuk pelanggan Embun Cafe.</p>
        <div style="display: flex; gap: 10px;">
            <button style="background: #fdf5d3; color: #856404; border: none; padding: 8px 20px; border-radius: 6px; font-weight: bold;">Lihat Laporan Stok</button>
            <button style="background: transparent; color: white; border: 1px solid rgba(255,255,255,0.5); padding: 8px 20px; border-radius: 6px;">Kelola Shift</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div class="card" style="display: flex; gap: 15px; align-items: center;">
            <div style="background: #e6f4ea; padding: 15px; border-radius: 8px; font-size: 20px;">🛍️</div>
            <div>
                <p style="margin: 0; font-size: 12px; color: #666;">Incoming Orders</p>
                <h2 style="margin: 5px 0;">24</h2>
                <p style="margin: 0; font-size: 11px; color: #1e8e3e;">↗ +8 dari sejam yang lalu</p>
            </div>
        </div>
        <div class="card" style="display: flex; gap: 15px; align-items: center;">
            <div style="background: #fdf5d3; padding: 15px; border-radius: 8px; font-size: 20px;">📅</div>
            <div>
                <p style="margin: 0; font-size: 12px; color: #666;">Today's Reservations</p>
                <h2 style="margin: 5px 0;">12</h2>
                <p style="margin: 0; font-size: 11px; color: #856404;">Next: Meja 4 (14:30)</p>
            </div>
        </div>
        <div class="card" style="display: flex; gap: 15px; align-items: center; border: 1px solid #fce8e6; background: #fffaf9;">
            <div style="background: #fce8e6; color: #dc3545; padding: 15px; border-radius: 8px; font-size: 20px;">⚠️</div>
            <div>
                <p style="margin: 0; font-size: 12px; color: #dc3545; font-weight: bold;">Low Stock Alerts</p>
                <h2 style="margin: 5px 0; color: #dc3545;">04</h2>
                <p style="margin: 0; font-size: 11px; color: #666;">Kopi Arabika, Susu Oat...</p>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <h4 style="margin: 0;">🛒 Pesanan Online Masuk</h4>
                <a href="#" style="font-size: 12px; color: #4c7c5f; text-decoration: none;">Lihat Semua</a>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
                <div style="display: flex; gap: 15px;">
                    <div style="width: 50px; height: 50px; background: #ddd; border-radius: 8px;"></div>
                    <div>
                        <strong style="display: block;">Iced Gula Aren Latte</strong>
                        <span style="font-size: 12px; color: #888;">Order #EB-2024-001 • Gojek</span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <strong style="display: block; margin-bottom: 5px;">Rp 28.000</strong>
                    <span class="badge" style="background: #e6f4ea; color: #4c7c5f;">DIPROSES</span>
                </div>
            </div>
        </div>

        <div class="card">
            <h4 style="margin: 0 0 15px 0;">📅 Verifikasi Reservasi</h4>
            <div style="background: #f4f3ed; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <div>
                        <strong style="display: block;">Ahmad Dhani</strong>
                        <span style="font-size: 11px; color: #666;">4 Tamu • Hari ini, 19:00</span>
                    </div>
                    <span style="background: #e2e8e4; font-size: 10px; padding: 2px 6px; border-radius: 4px; height: fit-content;">MEJA 12</span>
                </div>
                <div style="display: flex; gap: 5px;">
                    <button class="btn-primary" style="flex: 1; padding: 6px;">Verifikasi</button>
                    <button style="background: white; border: 1px solid #ddd; padding: 6px 10px; border-radius: 6px;">❌</button>
                </div>
            </div>
        </div>
    </div>
@endsection
