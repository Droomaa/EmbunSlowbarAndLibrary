<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API Embun Cafe</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .box { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; max-width: 400px; }
        input, button { display: block; margin-bottom: 10px; width: 100%; padding: 8px; }
    </style>
</head>
<body>

    <h1>UI Testing Embun Cafe</h1>

    <div class="box">
        <h3>1. Login Internal (Owner/Admin)</h3>
        <input type="text" id="username" placeholder="Username">
        <input type="password" id="password" placeholder="Password">
        <button onclick="testLogin()">Login</button>
        <p id="login-status" style="color: blue;"></p>
    </div>

    <div class="box">
        <h3>2. Tambah Menu (Harus Login)</h3>
        <input type="text" id="menuName" placeholder="Nama Menu">
        <input type="number" id="price" placeholder="Harga (Misal: 25000)">
        <input type="file" id="image">
        <button onclick="testTambahMenu()">Simpan Menu</button>
        <p id="menu-status" style="color: green;"></p>
    </div>

    <div class="box">
        <h3>3. Reservasi Tempat (Guest / Tanpa Login)</h3>
        <input type="text" id="res_name" placeholder="Nama Kamu">
        <input type="text" id="res_phone" placeholder="Nomor WhatsApp">
        <label style="display:block; margin-bottom:5px; font-size: 14px;">Tanggal & Jam Reservasi:</label>
        <input type="datetime-local" id="res_date">
        <input type="number" id="res_pax" placeholder="Jumlah Orang (Misal: 4)">
        <button onclick="testReservasi()">Buat Reservasi</button>
        <p id="res-status" style="color: orange;"></p>
    </div>

    <div class="box">
        <h3>4. Verifikasi Reservasi (Harus Login sbg Karyawan/Admin)</h3>
        <input type="number" id="verif_id" placeholder="ID Reservasi (Misal: 1)">
        <select id="verif_status" style="display:block; width:100%; margin-bottom:10px; padding:8px;">
            <option value="Approved">Approved (Terima)</option>
            <option value="Rejected">Rejected (Tolak)</option>
            <option value="Completed">Completed (Selesai)</option>
        </select>
        <button onclick="testVerifikasi()">Update Status</button>
        <p id="verif-status" style="font-weight: bold;"></p>
    </div>

    <script>
        // Set URL dasar API kamu
        const API_URL = 'http://127.0.0.1:8000/api';

        // 1. Fungsi Test Login
        async function testLogin() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const status = document.getElementById('login-status');

            status.innerText = "Loading...";

            try {
                const response = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();

                if (response.ok) {
                    // Simpan token ke memori browser
                    localStorage.setItem('embun_token', data.access_token);
                    status.innerText = `Sukses! Login sebagai: ${data.user.role}`;
                } else {
                    status.innerText = `Gagal: ${data.message}`;
                }
            } catch (error) {
                status.innerText = "Error jaringan!";
            }
        }

        // 2. Fungsi Test Tambah Menu
        async function testTambahMenu() {
            const token = localStorage.getItem('embun_token');
            const status = document.getElementById('menu-status');
            
            if (!token) {
                status.innerText = "Ditolak: Kamu belum login!";
                return;
            }

            status.innerText = "Uploading...";

            // Menggunakan FormData karena kita mengirim file gambar
            const formData = new FormData();
            formData.append('menuName', document.getElementById('menuName').value);
            formData.append('price', document.getElementById('price').value);
            formData.append('image', document.getElementById('image').files[0]);

            try {
                const response = await fetch(`${API_URL}/menus`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    status.innerText = "Sukses: Menu berhasil ditambah!";
                } else {
                    status.innerText = `Gagal: ${data.message || 'Cek inputanmu'}`;
                }
            } catch (error) {
                status.innerText = "Error jaringan!";
            }
        }

        // 3. Fungsi Test Reservasi (Guest)
        async function testReservasi() {
            const status = document.getElementById('res-status');
            status.innerText = "Memproses...";
            status.style.color = "orange";

            const payload = {
                customer_name: document.getElementById('res_name').value,
                phone_number: document.getElementById('res_phone').value,
                reservation_date: document.getElementById('res_date').value,
                pax: document.getElementById('res_pax').value
            };

            try {
                // Perhatikan: Tidak ada header Authorization Bearer Token di sini!
                const response = await fetch(`${API_URL}/reservations`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok) {
                    status.innerText = `Sukses: ${data.message}`;
                    status.style.color = "green";
                } else {
                    status.innerText = `Gagal: Cek kembali inputanmu!`;
                    status.style.color = "red";
                }
            } catch (error) {
                status.innerText = "Error jaringan!";
                status.style.color = "red";
            }
        }
        
        // 4. Fungsi Verifikasi Reservasi
        async function testVerifikasi() {
            const token = localStorage.getItem('embun_token');
            const statusLabel = document.getElementById('verif-status');
            const id = document.getElementById('verif_id').value;
            const newStatus = document.getElementById('verif_status').value;
            
            if (!token) {
                statusLabel.innerText = "Ditolak: Kamu belum login!";
                statusLabel.style.color = "red";
                return;
            }

            statusLabel.innerText = "Memproses...";
            statusLabel.style.color = "orange";

            try {
                const response = await fetch(`${API_URL}/reservations/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await response.json();

                if (response.ok) {
                    statusLabel.innerText = `Sukses: ${data.message}`;
                    statusLabel.style.color = "green";
                } else {
                    statusLabel.innerText = `Gagal: ${data.message}`;
                    statusLabel.style.color = "red";
                }
            } catch (error) {
                statusLabel.innerText = "Error jaringan!";
                statusLabel.style.color = "red";
            }
        }
    </script>

</body>
</html>
