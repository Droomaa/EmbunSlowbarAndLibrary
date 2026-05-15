@extends('layouts.owner')

@section('title', 'Account Management')

@section('content')
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <p style="color: #666; margin: 0;">Manage access for your cafe staff. Assign roles, monitor status.</p>
        <button style="background-color: #2e5a40; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">+ Add New Account</button>
    </div>

    <div class="grid-4">
        <div class="card">
            <p style="color: #888; font-size: 12px; margin: 0; text-transform: uppercase;">Total Users</p>
            <h2 style="margin: 10px 0 0 0;" id="val-users">12</h2>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 12px; margin: 0; text-transform: uppercase;">Active Now</p>
            <h2 style="margin: 10px 0 0 0; color: #28a745;">• 9</h2>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 12px; margin: 0; text-transform: uppercase;">Admin Roles</p>
            <h2 style="margin: 10px 0 0 0;">3</h2>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 12px; margin: 0; text-transform: uppercase;">System Health</p>
            <h2 style="margin: 10px 0 0 0;">Optimized</h2>
        </div>
    </div>

    <div class="card">
        <table id="accounts-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Ahmad Rizky</strong></td>
                    <td style="color: #888;">@rizky_owner</td>
                    <td><span style="background: #eee; padding: 3px 8px; border-radius: 10px; font-size: 12px;">Owner</span></td>
                    <td><span class="text-green">• Active</span></td>
                    <td>
                        <button onclick="showDeleteModal('Budi Pratama')" style="border: none; background: transparent; cursor: pointer; color: #888;">🗑️</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="delete-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
        <div class="card" style="width: 350px; text-align: center;">
            <div style="background: #ffebe9; color: #dc3545; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                🗑️
            </div>
            <h3 style="margin-top: 0;">Hapus Akun?</h3>
            <p style="color: #666; font-size: 14px;">Apakah Anda yakin ingin menghapus akun <span id="delete-target-name" style="font-weight: bold;"></span>? Tindakan ini tidak dapat dibatalkan.</p>
            
            <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                <button style="background: #dc3545; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-weight: bold;">Hapus</button>
                <button onclick="hideDeleteModal()" style="background: #f4f4f4; color: #555; border: none; padding: 12px; border-radius: 5px; cursor: pointer;">Batal</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // JS Logic untuk Modal
    function showDeleteModal(name) {
        document.getElementById('delete-target-name').innerText = name;
        document.getElementById('delete-modal').style.display = 'flex';
    }

    function hideDeleteModal() {
        document.getElementById('delete-modal').style.display = 'none';
    }
</script>
@endsection
