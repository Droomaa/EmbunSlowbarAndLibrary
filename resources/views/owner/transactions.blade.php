@extends('layouts.owner')

@section('title', 'Transaction Management')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0;">Transaction History</h3>
            <p style="color: #666; font-size: 14px; margin: 5px 0 0 0;">Review and manage all customer activities within the cafe ecosystem.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button style="background-color: #2e5a40; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">+ Add Transaction</button>
            <button style="background-color: white; border: 1px solid #ddd; padding: 10px 20px; border-radius: 5px; cursor: pointer;">📥 Export CSV</button>
        </div>
    </div>

    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
        <select style="padding: 10px; border-radius: 5px; border: 1px solid #ddd; width: 200px;">
            <option>📅 Last 7 Days</option>
        </select>
        <select style="padding: 10px; border-radius: 5px; border: 1px solid #ddd; width: 200px;">
            <option>All Types</option>
            <option>Dine-in</option>
            <option>Takeaway</option>
        </select>
        <input type="text" placeholder="ID, Customer, or Payment..." style="padding: 10px; border-radius: 5px; border: 1px solid #ddd; flex: 1;">
    </div>

    <div class="card">
        <table id="transaction-table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Order Type</th>
                    <th>Total Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-green" style="font-weight: bold;">#EMB-2024-001</td>
                    <td style="color: #666; font-size: 14px;">Oct 24, 2024<br>09:15 AM</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="background: #eee; padding: 5px; border-radius: 50%; font-size: 12px;">AL</span>
                            <strong>Aria Laurent</strong>
                        </div>
                    </td>
                    <td>🍽️ Dine-in</td>
                    <td><strong>$42.50</strong></td>
                    <td><span class="badge badge-success">Paid</span></td>
                    <td><span class="badge" style="background: #e2e8e4; color: #2e5a40;">Completed</span></td>
                    <td>
                        <button style="border: none; background: transparent; cursor: pointer; color: #2e5a40;">👁️</button>
                        <button style="border: none; background: transparent; cursor: pointer; color: #2e5a40;">✏️</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
