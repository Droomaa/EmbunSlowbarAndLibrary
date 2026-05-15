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
            <h2 style="margin: 10px 0 0 0;" id="val-users">0</h2>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 12px; margin: 0; text-transform: uppercase;">Active Now</p>
            <h2 style="margin: 10px 0 0 0; color: #28a745;">• 9</h2>
        </div>
        <div class="card">
            <p style="color: #888; font-size: 12px; margin: 0; text-transform: uppercase;">Admin Roles</p>
            <h2 style="margin: 10px 0 0 0;" id="val-admin">0</h2>
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
            <tbody id="accounts-tbody">
                <tr><td colspan="5" style="text-align: center; color: #888;">Memuat data...</td></tr>
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
    // JS Logic untuk Modal Hapus
    function showDeleteModal(name, id) {
        document.getElementById('delete-target-name').innerText = name;
        document.getElementById('delete-modal').style.display = 'flex';
        
        // Nanti kamu bisa tambahkan fungsi fetch DELETE ke API di sini pakai id
        console.log("Akan menghapus user ID:", id);
    }

    function hideDeleteModal() {
        document.getElementById('delete-modal').style.display = 'none';
    }

    // Fungsi Utama: Mengambil Data dari Backend
    document.addEventListener('DOMContentLoaded', async () => {
        // Ambil token dari memori browser yang didapat saat login di test-ui
        const token = localStorage.getItem('embun_token');
        const tbody = document.getElementById('accounts-tbody');

        if (!token) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Akses ditolak! Silakan login sebagai Owner terlebih dahulu.</td></tr>';
            return;
        }

        try {
            const response = await fetch(`${API_URL}/owner/accounts`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: red;">Gagal mengambil data: ${data.message || 'Unauthorized'}</td></tr>`;
                return;
            }

            tbody.innerHTML = '';
            
            const users = data.data ? data.data : data;
            let adminCount = 0;

            users.forEach(user => {
                if (user.role === 'Admin') adminCount++;

                let roleColorBg = '#eee';
                let roleColorText = '#333';
                if (user.role === 'Admin') { roleColorBg = '#d4edda'; roleColorText = '#155724'; }
                if (user.role === 'Staff' || user.role === 'Karyawan') { roleColorBg = '#fff3cd'; roleColorText = '#856404'; }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${user.name}</strong></td>
                    <td style="color: #888;">@${user.username}</td>
                    <td><span style="background: ${roleColorBg}; color: ${roleColorText}; padding: 3px 8px; border-radius: 10px; font-size: 12px; font-weight: bold;">${user.role}</span></td>
                    <td><span class="text-green">• Active</span></td>
                    <td>
                        <button onclick="showDeleteModal('${user.name}', ${user.id})" style="border: none; background: transparent; cursor: pointer; color: #888; font-size: 16px;">🗑️</button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('val-users').innerText = users.length;
            document.getElementById('val-admin').innerText = adminCount;

        } catch (error) {
            console.error("Terjadi kesalahan:", error);
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Error jaringan! Pastikan server API menyala.</td></tr>';
        }
    });
</script>
@endsection
