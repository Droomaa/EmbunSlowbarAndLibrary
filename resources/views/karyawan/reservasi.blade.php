@extends('layouts.karyawan')

@section('title', 'Verification Queue')

@section('content')
    <p style="color: #666; margin-top: -15px; margin-bottom: 25px;">Review and approve customer table bookings for today and tomorrow.</p>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px;">
        <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
            <div><p style="color: #888; font-size: 11px; margin: 0;">PENDING</p><h2 style="margin: 5px 0 0 0; color: #856404;">12</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">⏳</span>
        </div>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
            <div><p style="color: #888; font-size: 11px; margin: 0;">CONFIRMED</p><h2 style="margin: 5px 0 0 0; color: #1e8e3e;">48</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">✅</span>
        </div>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
            <div><p style="color: #888; font-size: 11px; margin: 0;">AVAILABLE TABLES</p><h2 style="margin: 5px 0 0 0;">08</h2></div>
            <span style="font-size: 20px; opacity: 0.5;">🪑</span>
        </div>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
            <div><p style="color: #888; font-size: 11px; margin: 0;">WAITLIST</p><h2 style="margin: 5px 0 0 0;">03</h2></div>
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
            <tbody>
                <tr>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; display: flex; gap: 10px; align-items: center;">
                        <span style="background: #e6f4ea; color: #1e8e3e; padding: 10px; border-radius: 50%; font-size: 12px; font-weight: bold;">AS</span>
                        <div><strong>Aditya Surya</strong><br><span style="font-size: 11px; color: #888;">aditya.s@email.com</span></div>
                    </td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; font-size: 13px;">Oct 24, 2023<br><strong>19:00 PM</strong></td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; font-size: 13px;">👥 4 People</td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee; font-size: 13px; color: #666;">Birthday celebration, window seat preferred.</td>
                    <td style="padding: 15px 10px; border-bottom: 1px solid #eee;"><span class="badge badge-warning">PENDING</span></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
