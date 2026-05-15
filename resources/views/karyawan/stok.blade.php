@extends('layouts.karyawan')

@section('title', 'Data Stok Bahan')

@section('content')
    <p style="color: #666; margin-top: -15px; margin-bottom: 25px;">Pantau dan kelola ketersediaan bahan baku operasional harian.</p>

    <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div class="card" style="background: #f4f7f5; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #888; font-size: 11px; margin: 0; text-transform: uppercase;">Total Bahan</p>
                <h2 style="margin: 5px 0 0 0; font-size: 32px;">124</h2>
            </div>
            <span style="color: #1e8e3e; font-size: 12px; font-weight: bold;">+4 minggu ini</span>
        </div>
        <div class="card" style="background: #fce8e6; border: 1px solid #fad2cf; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #dc3545; font-size: 11px; margin: 0; text-transform: uppercase;">Stok Habis</p>
                <h2 style="margin: 5px 0 0 0; color: #dc3545; font-size: 32px;">3</h2>
            </div>
            <span style="font-size: 24px; color: #dc3545;">⚠️</span>
        </div>
        <div class="card" style="background: #fffdf5; border: 1px solid #fdf5d3; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #856404; font-size: 11px; margin: 0; text-transform: uppercase;">Stok Menipis</p>
                <h2 style="margin: 5px 0 0 0; color: #856404; font-size: 32px;">12</h2>
            </div>
            <span style="font-size: 24px; color: #856404;">❗</span>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 11px; margin: 0 0 10px 0; text-transform: uppercase;">Kategori Cepat</p>
            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                <span style="background: #e6f4ea; color: #1e8e3e; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">Coffee</span>
                <span style="background: #f0f0f0; color: #555; padding: 4px 8px; border-radius: 12px; font-size: 11px;">Dairy</span>
                <span style="background: #f0f0f0; color: #555; padding: 4px 8px; border-radius: 12px; font-size: 11px;">Syrups</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; gap: 10px;">
                <button style="background: white; border: 1px solid #ddd; padding: 8px 15px; border-radius: 20px; color: #555;">Semua Kategori</button>
                <button style="background: white; border: 1px solid #ddd; padding: 8px 15px; border-radius: 20px; color: #555;">Status: Semua</button>
                <input type="text" placeholder="🔍 Cari bahan..." style="padding: 8px 15px; border-radius: 20px; border: 1px solid #ddd; background: #f9f9f9;">
            </div>
            <button class="btn-primary">+ Tambah Stok</button>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">MATERIAL NAME</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">QUANTITY</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">UNIT</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">STATUS</th>
                    <th style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 11px; color: #888;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee;"><strong>Arabica Gayo Beans</strong></td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee;"><strong>12.5</strong></td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; color: #888;">kg</td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee;"><span class="badge badge-success">Safe</span></td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; color: #aaa; cursor: pointer;">✏️ 🗑️</td>
                </tr>
                <tr>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee;"><strong>Vanilla Syrup</strong></td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; color: #dc3545;"><strong>0.0</strong></td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; color: #888;">Bottle</td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee;"><span class="badge" style="background: #dc3545; color: white;">Out of Stock</span></td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; color: #aaa; cursor: pointer;">✏️ 🗑️</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="background: #f4f7f5; padding: 15px; border-radius: 8px; margin-top: 20px; display: flex; gap: 15px; align-items: flex-start;">
        <span style="font-size: 20px;">💡</span>
        <div>
            <strong style="display: block; color: #2e5a40;">Operational Tip</strong>
            <p style="margin: 5px 0 0 0; font-size: 13px; color: #555;">Stok yang berstatus "Low" akan otomatis muncul dalam daftar usulan pesanan pengadaan besok pagi. Pastikan kuantitas tercatat akurat.</p>
        </div>
    </div>
@endsection
