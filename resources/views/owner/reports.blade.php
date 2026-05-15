@extends('layouts.owner')

@section('title', 'Sales Reports')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
        <div class="card" style="display: flex; flex-direction: column; justify-content: center;">
            <p style="color: #888; font-size: 12px; margin: 0; text-transform: uppercase;">Total Revenue</p>
            <h2 style="margin: 10px 0 10px 0; color: #2e5a40; font-size: 28px;">$12,450.00</h2>
            <span class="badge badge-success" style="width: fit-content;">↗ +12.5% from last month</span>
        </div>
        <div class="card" style="display: flex; flex-direction: column; justify-content: center;">
            <p style="color: #888; font-size: 12px; margin: 0; text-transform: uppercase;">Avg. Order Value</p>
            <h2 style="margin: 10px 0 10px 0; color: #666; font-size: 28px;">$24.80</h2>
            <span class="badge badge-warning" style="width: fit-content; background: #fdf5d3;">📊 Stable vs. last week</span>
        </div>
        <div style="background-color: #2e5a40; color: white; border-radius: 8px; padding: 20px; display: flex; flex-direction: column; justify-content: flex-end; position: relative; overflow: hidden;">
            <div style="position: relative; z-index: 2;">
                <p style="margin: 0; font-size: 12px; opacity: 0.8;">CAFE SNAPSHOT</p>
                <h3 style="margin: 5px 0 0 0; font-style: italic;">"Rooted in Flavor"</h3>
            </div>
            <div style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);"></div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px;">
            <select style="padding: 10px; border-radius: 5px; border: 1px solid #ddd; width: 220px;">
                <option>📅 Oct 01, 2024 - Oct 31, 2024</option>
            </select>
            <select style="padding: 10px; border-radius: 5px; border: 1px solid #ddd; width: 150px;">
                <option>All Methods</option>
                <option>Credit Card</option>
                <option>Cash</option>
            </select>
        </div>
        <button style="background-color: #f4f4f4; color: #333; border: 1px solid #ddd; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">📥 Export Report</button>
    </div>

    <div class="card">
        <table id="sales-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>Total Sales</th>
                    <th>Payment Method</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color: #555;">Oct 24, 2024</td>
                    <td style="color: #888;">#EMB-8821</td>
                    <td><strong>$42.50</strong></td>
                    <td><span style="border: 1px solid #ddd; padding: 4px 10px; border-radius: 15px; font-size: 12px; color: #666;">Credit Card</span></td>
                    <td><button style="border: none; background: transparent; cursor: pointer; color: #888;">👁️</button></td>
                </tr>
                <tr>
                    <td style="color: #555;">Oct 24, 2024</td>
                    <td style="color: #888;">#EMB-8820</td>
                    <td><strong>$18.25</strong></td>
                    <td><span style="background: #fdf5d3; color: #856404; padding: 4px 10px; border-radius: 15px; font-size: 12px;">Cash</span></td>
                    <td><button style="border: none; background: transparent; cursor: pointer; color: #888;">👁️</button></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
