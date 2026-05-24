@extends('layouts.owner')

@section('title', 'Account Management')

@section('content')
    <style>
        /* CSS Untuk Pop-up Form (Modal) */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-title { margin: 0; font-size: 18px; color: #333; }
        .close-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #888; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; color: #555; margin-bottom: 5px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none; box-sizing: border-box; }
        .form-control:focus { border-color: #2e5a40; }
        
        .btn-submit { background: #2e5a40; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-submit:hover { background: #1e4620; }
    </style>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <p style="margin: 0; color: #888; font-size: 14px;">Manage access for your cafe staff. Assign roles, monitor status.</p>
        </div>
        <button onclick="openModal()" style="background: #2e5a40; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            + Add New Account
        </button>
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px;">
        <div class="card" style="background: white; padding: 20px; border-radius: 12px;">
            <span style="font-size: 11px; color: #888; font-weight: bold; text-transform: uppercase;">Total Users</span>
            <h2 id="val-total-users" style="margin: 10px 0 0 0; font-size: 28px; color: #333;">0</h2>
        </div>
        <div class="card" style="background: white; padding: 20px; border-radius: 12px;">
            <span style="font-size: 11px; color: #888; font-weight: bold; text-transform: uppercase;">Active Now</span>
            <h2 id="val-active-now" style="margin: 10px 0 0 0; font-size: 28px; color: #27ae60;">0</h2>
        </div>
        <div class="card" style="background: white; padding: 20px; border-radius: 12px;">
            <span style="font-size: 11px; color: #888; font-weight: bold; text-transform: uppercase;">Admin Roles</span>
            <h2 id="val-admin-roles" style="margin: 10px 0 0 0; font-size: 28px; color: #333;">0</h2>
        </div>
        <div class="card" style="background: white; padding: 20px; border-radius: 12px;">
            <span style="font-size: 11px; color: #888; font-weight: bold; text-transform: uppercase;">System Health</span>
            <h2 style="margin: 10px 0 0 0; font-size: 24px; color: #333;">Optimized</h2>
        </div>
    </div>

    <div class="card" style="background: white; padding: 25px; border-radius: 12px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; font-size: 11px; color: #888;">
                    <th style="padding: 15px 5px;">NAME</th>
                    <th style="padding: 15px 5px;">USERNAME / EMAIL</th>
                    <th style="padding: 15px 5px;">ROLE</th>
                    <th style="padding: 15px 5px;">STATUS</th>
                    <th style="padding: 15px 5px; text-align: center;">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="accounts-tbody" style="font-size: 13px; color: #555;">
                <tr><td colspan="5" style="text-align:center; padding: 20px;">⏳ Memuat data...</td></tr>
            </tbody>
        </table>
    </div>

    <div id="modal-add-account" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Account</h3>
                <button class="close-btn" onclick="closeModal()">×</button>
            </div>
            
            <form id="add-account-form" onsubmit="submitNewAccount(event)">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="input-name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="form-group">
                    <label>Username / Email</label>
                    <input type="text" id="input-username" class="form-control" placeholder="budi@embun.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="input-password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select id="input-role" class="form-control" required>
                        <option value="Staff">Staff / Barista</option>
                        <option value="Admin">Admin / Manager</option>
                        <option value="Owner">Owner</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-submit" id="btn-save">Create Account</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const API_URL_ACC = 'http://127.0.0.1:8000/api';
    const token = localStorage.getItem('embun_token');

    document.addEventListener('DOMContentLoaded', loadAccountsData);

    // Buka Tutup Modal
    window.openModal = function() {
        document.getElementById('modal-add-account').style.display = 'flex';
    }
    window.closeModal = function() {
        document.getElementById('modal-add-account').style.display = 'none';
        document.getElementById('add-account-form').reset();
    }

    // Load Data Tabel dan Metrik
    async function loadAccountsData() {
        const tbody = document.getElementById('accounts-tbody');
        try {
            const response = await fetch(`${API_URL_ACC}/owner/accounts/data`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await response.json();

            if (response.ok) {
                // Update Metrik
                document.getElementById('val-total-users').innerText = result.total_users;
                document.getElementById('val-active-now').innerText = result.active_now;
                document.getElementById('val-admin-roles').innerText = result.admin_roles;

                // Render Tabel
                tbody.innerHTML = '';
                if(result.users && result.users.length > 0) {
                    result.users.forEach(user => {
                        const role = user.role || 'Karyawan';
                        const badgeColor = role.toLowerCase() === 'owner' || role.toLowerCase() === 'admin' 
                                           ? 'background: #e6f4ea; color: #1e8e3e;' 
                                           : 'background: #fdfdfa; color: #555; border: 1px solid #ddd;';
                        
                        // Menyesuaikan jika di database kamu namanya 'email' atau 'username'
                        const userEmail = user.email || user.username || '-';

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;"><strong>${user.name}</strong></td>
                            <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">${userEmail}</td>
                            <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">
                                <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; ${badgeColor}">${role}</span>
                            </td>
                            <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;"><span style="color: #27ae60;">● Active</span></td>
                            <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; text-align: center;">
                                <button style="background:none; border:none; color:#3498db; cursor:pointer;" title="Edit">✏️</button>
                                <button style="background:none; border:none; color:#e74c3c; cursor:pointer;" title="Delete">🗑️</button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                }
            } else {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: red; padding: 20px;">Gagal mengambil data: Anda tidak memiliki izin.</td></tr>`;
            }
        } catch (error) {
            console.error("Error:", error);
        }
    }

    // Fungsi Submit Form Tambah Akun ke Backend
    window.submitNewAccount = async function(e) {
        e.preventDefault(); // Mencegah halaman reload

        const btnSave = document.getElementById('btn-save');
        btnSave.innerText = 'Menyimpan...';
        btnSave.disabled = true;

        const data = {
            name: document.getElementById('input-name').value,
            username: document.getElementById('input-username').value,
            password: document.getElementById('input-password').value,
            role: document.getElementById('input-role').value,
        };

        try {
            const response = await fetch(`${API_URL_ACC}/owner/accounts/add`, {
                method: 'POST',
                headers: { 
                    'Authorization': `Bearer ${token}`, 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Berhasil! Akun baru telah ditambahkan.');
                closeModal();
                loadAccountsData(); 
            } else {
                // Tangkap pesan error dari CCTV Controller
                const errData = await response.json();
                alert('GAGAL: ' + errData.error);
            }
        } catch (error) {
            alert('Terjadi kesalahan koneksi server.');
        } finally {
            btnSave.innerText = 'Create Account';
            btnSave.disabled = false;
        }
    }
</script>
@endsection