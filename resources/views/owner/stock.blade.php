@extends('layouts.owner')

@section('title', 'Stock Material Report')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 15px; margin-bottom: 20px;">
        <div class="card" style="display: flex; flex-direction: column; justify-content: center; position: relative;">
            <p style="color: #888; font-size: 12px; margin: 0; text-transform: uppercase; text-align: right; position: absolute; top: 15px; right: 20px;">Overall</p>
            <div style="width: 35px; height: 35px; background: #e6f4ea; color: #2e5a40; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">📋</div>
            <p style="color: #888; font-size: 14px; margin: 0;">Total Items</p>
            <h2 style="margin: 5px 0 0 0; font-size: 32px; color: #333;">148</h2>
        </div>
        
        <div class="card" style="background-color: #fce8e6; border: 1px solid #fad2cf; display: flex; flex-direction: column; justify-content: center; position: relative;">
            <p style="color: #dc3545; font-size: 12px; margin: 0; font-weight: bold; text-transform: uppercase; text-align: right; position: absolute; top: 15px; right: 20px;">Critical</p>
            <div style="width: 35px; height: 35px; background: #f8d7da; color: #dc3545; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">⚠️</div>
            <p style="color: #dc3545; font-size: 14px; margin: 0;">Needing Attention</p>
            <h2 style="margin: 5px 0 0 0; font-size: 32px; color: #dc3545;">12</h2>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 style="margin: 0;">Stock Distribution</h4>
                <span style="color: #28a745; font-size: 12px; font-weight: bold;">Updated just now</span>
            </div>
            <div style="width: 100%; height: 12px; border-radius: 6px; display: flex; overflow: hidden; margin-bottom: 15px;">
                <div style="width: 75%; background-color: #2e5a40;"></div>
                <div style="width: 15%; background-color: #d39e00;"></div>
                <div style="width: 10%; background-color: #dc3545;"></div>
            </div>
            <div style="display: flex; gap: 20px; font-size: 12px; color: #666;">
                <span><span style="color: #2e5a40;">●</span> Safe (75%)</span>
                <span><span style="color: #d39e00;">●</span> Low (15%)</span>
                <span><span style="color: #dc3545;">●</span> Out (10%)</span>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; gap: 10px;">
            <button style="background-color: #2e5a40; color: white; border: none; padding: 8px 16px; border-radius: 20px; cursor: pointer; font-weight: bold;">All Materials</button>
            <button style="background-color: transparent; color: #666; border: none; padding: 8px 16px; border-radius: 20px; cursor: pointer;">Coffee Beans</button>
            <button style="background-color: transparent; color: #666; border: none; padding: 8px 16px; border-radius: 20px; cursor: pointer;">Dairy & Milk</button>
            <button style="background-color: transparent; color: #666; border: none; padding: 8px 16px; border-radius: 20px; cursor: pointer;">Syrups</button>
        </div>
        <div style="display: flex; gap: 10px;">
            <button style="background-color: white; border: 1px solid #ddd; padding: 8px 15px; border-radius: 5px; cursor: pointer; color: #555;">⚙️ Filter</button>
            <button style="background-color: white; border: 1px solid #ddd; padding: 8px 15px; border-radius: 5px; cursor: pointer; color: #555;">📥 Export PDF</button>
        </div>
    </div>

    <div class="card">
        <table id="stock-table">
            <thead>
                <tr>
                    <th>Material Name</th>
                    <th>Category</th>
                    <th>Current Quantity</th>
                    <th>Unit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 30px; height: 30px; background: #8B4513; border-radius: 50%; display: inline-block;"></div>
                        <strong>Arabica House Blend</strong>
                    </td>
                    <td style="color: #666;">Coffee Beans</td>
                    <td><strong>2.5</strong></td>
                    <td style="color: #888;">kg</td>
                    <td><span class="badge badge-warning" style="background: #fdf5d3; color: #856404;">Low</span></td>
                </tr>
                
                <tr>
                    <td style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 30px; height: 30px; background: #e2e8e4; border-radius: 50%; display: inline-block;"></div>
                        <strong>Oat Milk Barista Edition</strong>
                    </td>
                    <td style="color: #666;">Dairy & Milk</td>
                    <td><strong>48.0</strong></td>
                    <td style="color: #888;">Liters</td>
                    <td><span class="badge badge-success" style="background: #e6f4ea; color: #1e8e3e;">Safe</span></td>
                </tr>

                <tr>
                    <td style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 30px; height: 30px; background: #d2b48c; border-radius: 50%; display: inline-block;"></div>
                        <strong>Brown Sugar Sachet</strong>
                    </td>
                    <td style="color: #666;">Sweeteners</td>
                    <td style="color: #dc3545;"><strong>0.0</strong></td>
                    <td style="color: #888;">Box</td>
                    <td><span class="badge" style="background: #fce8e6; color: #d93025;">Out of Stock</span></td>
                </tr>

                <tr>
                    <td style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 30px; height: 30px; background: #f5fffa; border: 1px solid #ddd; border-radius: 50%; display: inline-block;"></div>
                        <strong>Vanilla Syrup (1L)</strong>
                    </td>
                    <td style="color: #666;">Syrups</td>
                    <td><strong>12.0</strong></td>
                    <td style="color: #888;">Bottles</td>
                    <td><span class="badge badge-success" style="background: #e6f4ea; color: #1e8e3e;">Safe</span></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
