@extends('layouts.owner')

@section('title', 'Menu Management')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0;">Catalogue Overview</h3>
            <p style="color: #666; font-size: 14px; margin: 5px 0 0 0;">Manage your cafe items, pricing, and daily availability.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <select style="padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                <option>All Categories</option>
                <option>Coffee</option>
                <option>Main Course</option>
            </select>
            <button style="background-color: #2e5a40; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">+ Add New Menu</button>
        </div>
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <table id="menu-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 40px; height: 40px; background: #ddd; border-radius: 50%;"></div>
                        <div>
                            <strong style="display: block;">Caramel Macchiato</strong>
                            <small style="color: #888;">Classic Espresso Base</small>
                        </div>
                    </td>
                    <td>Coffee</td>
                    <td><strong>Rp 32.000</strong></td>
                    <td><span class="badge badge-success" style="background: #e6f4ea; color: #1e8e3e;">Available</span></td>
                    <td>
                        <button style="border: none; background: transparent; cursor: pointer; color: #888;">✏️</button>
                        <button style="border: none; background: transparent; cursor: pointer; color: #888;">🗑️</button>
                    </td>
                </tr>
                <tr>
                    <td style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 40px; height: 40px; background: #ddd; border-radius: 50%;"></div>
                        <div>
                            <strong style="display: block;">Dark Lava Melt</strong>
                            <small style="color: #888;">Premium 70% Cocoa</small>
                        </div>
                    </td>
                    <td>Snacks</td>
                    <td><strong>Rp 45.000</strong></td>
                    <td><span class="badge badge-warning" style="background: #fce8e6; color: #d93025;">Unavailable</span></td>
                    <td>
                        <button style="border: none; background: transparent; cursor: pointer; color: #888;">✏️</button>
                        <button style="border: none; background: transparent; cursor: pointer; color: #888;">🗑️</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
        <div class="card" style="background-color: #fcefb4; border: none;">
            <p style="color: #856404; font-size: 12px; font-weight: bold; margin: 0;">📈 Most Popular</p>
            <h2 style="margin: 15px 0 5px 0; color: #533f03;">Caramel Macchiato</h2>
            <p style="color: #856404; font-size: 14px; margin: 0;">124 orders this week</p>
        </div>
        <div class="card" style="background-color: #f4f4f4;">
            <p style="color: #555; font-size: 12px; font-weight: bold; margin: 0;">📋 Stock Alerts</p>
            <h2 style="margin: 15px 0 5px 0;">3 Items</h2>
            <p style="color: #777; font-size: 14px; margin: 0;">Require immediate restocking</p>
        </div>
        <div class="card" style="background-color: #f4f4f4;">
            <p style="color: #555; font-size: 12px; font-weight: bold; margin: 0;">🍴 Total Catalog</p>
            <h2 style="margin: 15px 0 5px 0;">42 Items</h2>
            <p style="color: #777; font-size: 14px; margin: 0;">Across 4 main categories</p>
        </div>
    </div>
@endsection
