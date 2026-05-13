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
<button onclick="loadDropdownData()" style="background-color: #28a745; color: white; margin-bottom: 20px;">Refresh Data Dropdown</button>
    <h1>UI Testing Embun Cafe</h1>

    <div class="box">
    <h3>1. Login Internal (Owner/Admin/Staff)</h3>
    <input type="text" id="log_user" placeholder="Username">
    <input type="password" id="log_pass" placeholder="Password">
    <button type="button" onclick="testLogin()">Login</button>
    <p id="login-status" style="font-weight: bold;"></p>
    <button type="button" onclick="testLogout()" style="background-color: #dc3545; color: white; margin-top: 10px; padding: 10px; border: none; border-radius: 4px; cursor: pointer;">Logout</button>
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
        <select id="verif_id" style="display:block; width:100%; margin-bottom:10px; padding:8px;">
            <option value="">-- Pilih Reservasi --</option>
        </select>
        <select id="verif_status" style="display:block; width:100%; margin-bottom:10px; padding:8px;">
            <option value="Approved">Approved (Terima)</option>
            <option value="Rejected">Rejected (Tolak)</option>
            <option value="Completed">Completed (Selesai)</option>
        </select>
        <button onclick="testVerifikasi()">Update Status</button>
        <p id="verif-status" style="font-weight: bold;"></p>
    </div>

    <div class="box">
        <h3>5. Buat Pesanan (Guest)</h3>
        <input type="text" id="order_name" placeholder="Nama Pemesan">
        <input type="text" id="order_table" placeholder="Nomor Meja (Opsional)">
        <p style="font-size: 14px; margin-bottom: 5px;">Pesan Menu (Satu item dulu untuk test):</p>
        <select id="order_menu_id" style="display:block; width:100%; margin-bottom:10px; padding:8px;">
            <option value="">-- Pilih Menu --</option>
        </select>
        <input type="number" id="order_qty" placeholder="Jumlah Porsi (Misal: 2)">
        <button onclick="testBuatPesanan()">Order Sekarang</button>
        <p id="order-status" style="font-weight: bold;"></p>
    </div>

    <div class="box">
        <h3>6. Update Status Pesanan (Staff/Admin)</h3>
        <select id="verif_order_id" style="display:block; width:100%; margin-bottom:10px; padding:8px;">
            <option value="">-- Pilih Pesanan --</option>
        </select>
        <select id="verif_order_status" style="display:block; width:100%; margin-bottom:10px; padding:8px;">
            <option value="Processing">Processing (Sedang Dibuat)</option>
            <option value="Completed">Completed (Selesai)</option>
            <option value="Canceled">Canceled (Batal)</option>
        </select>
        <button onclick="testUpdatePesanan()">Update Pesanan</button>
        <p id="verif-order-status" style="font-weight: bold;"></p>
    </div>

    <div class="box">
        <h3>7. Tambah Stok Bahan (Staff/Admin)</h3>
        <input type="text" id="inv_name" placeholder="Nama Bahan (Misal: Biji Kopi Gayo)">
        <input type="number" id="inv_qty" placeholder="Jumlah (Misal: 2.5)">
        <select id="inv_unit" style="display:block; width:100%; margin-bottom:10px; padding:8px;">
            <option value="Kg">Kilogram (Kg)</option>
            <option value="Gram">Gram</option>
            <option value="Liter">Liter</option>
            <option value="Ml">Mililiter (Ml)</option>
            <option value="Pcs">Pcs</option>
        </select>
        <button onclick="testTambahStok()">Simpan Stok Baru</button>
        <p id="inv-status" style="font-weight: bold;"></p>
    </div>

    <script>
        // Fungsi untuk Logout
        async function testLogout() {
            const token = localStorage.getItem('embun_token');
            const statusLabel = document.getElementById('login-status');
            
            if (!token) {
                alert("Kamu belum login, tidak ada yang perlu di-logout!");
                return;
            }

            statusLabel.innerText = "Proses logout...";
            statusLabel.style.color = "orange";

            try {
                // Tembak API Logout
                const response = await fetch(`${API_URL}/logout`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}` // Wajib bawa token untuk dihapus di backend
                    }
                });

                if (response.ok) {
                    // Hapus token dari browser
                    localStorage.removeItem('embun_token');
                    
                    statusLabel.innerText = "Sukses: Berhasil Logout! Silakan login kembali.";
                    statusLabel.style.color = "blue";
                    
                    // Refresh data dropdown supaya data yang butuh login (reservasi/order) hilang dari form
                    loadDropdownData();
                    
                    alert("Kamu telah berhasil logout.");
                } else {
                    statusLabel.innerText = "Gagal Logout. Token mungkin sudah kadaluarsa.";
                    statusLabel.style.color = "red";
                    localStorage.removeItem('embun_token'); // Tetap hapus di browser buat jaga-jaga
                }
            } catch (error) {
                console.error("Error Logout:", error);
                statusLabel.innerText = "Error jaringan saat logout!";
                statusLabel.style.color = "red";
            }
        }
        // Fungsi untuk mengambil data dan mengisi dropdown
        async function loadDropdownData() {
            const token = localStorage.getItem('embun_token');
            
           // 1. Load Data Menu (Public)
            try {
                const resMenu = await fetch(`${API_URL}/menus`);
                const responseData = await resMenu.json();
                
                // Deteksi otomatis: apakah datanya dibungkus "data" atau langsung array
                const menus = responseData.data ? responseData.data : responseData;

                const menuSelect = document.getElementById('order_menu_id');
                menuSelect.innerHTML = '<option value="">-- Pilih Menu --</option>'; // Reset
                
                menus.forEach(m => {
                    menuSelect.innerHTML += `<option value="${m.id}">${m.menuName} (Rp ${m.price})</option>`;
                });
            } catch (e) {
                console.error("Error Detail Load Menu:", e);
                alert("Gagal mengambil data menu. Cek inspect element (F12) -> tab Console!");
            }

            // 2. Load Data Reservasi & Order (Butuh Login)
            if (token) {
                try {
                    // Load Reservasi
                    const resResv = await fetch(`${API_URL}/reservations`, {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    const resvs = await resResv.json();
                    const resvSelect = document.getElementById('verif_id');
                    resvSelect.innerHTML = '<option value="">-- Pilih Reservasi --</option>';
                    resvs.forEach(r => {
                        resvSelect.innerHTML += `<option value="${r.reservation_id}">${r.customer_name} - ${r.reservation_date} (${r.status})</option>`;
                    });

                    // Load Order
                    const resOrder = await fetch(`${API_URL}/orders`, {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    const orders = await resOrder.json();
                    const orderSelect = document.getElementById('verif_order_id');
                    orderSelect.innerHTML = '<option value="">-- Pilih Pesanan --</option>';
                    orders.forEach(o => {
                        orderSelect.innerHTML += `<option value="${o.order_id}">Order #${o.order_id} - ${o.customer_name} (${o.status})</option>`;
                    });
                } catch (e) { console.log("Gagal load reservasi/order"); }
            } else {
                alert("Login sebagai Admin/Staff dulu untuk meload data dropdown Reservasi & Order!");
            }
        }

        // Otomatis jalankan fungsi saat halaman pertama kali dibuka
        window.onload = loadDropdownData;
        // Set URL dasar API kamu
        const API_URL = 'http://127.0.0.1:8000/api';

        // 1. Fungsi Test Login
        async function testLogin() {
            // Pengecekan Token Zombie
            const existingToken = localStorage.getItem('embun_token');
            if (existingToken) {
                alert("⛔ Ditolak: Kamu masih dalam keadaan Login! Silakan Logout terlebih dahulu sebelum login dengan akun lain.");
                return;
            }

            // Ambil ID yang persis sama dengan HTML di atas
            const usernameInput = document.getElementById('log_user');
            const passwordInput = document.getElementById('log_pass');
            const statusLabel = document.getElementById('login-status');

            // Cek pencegahan error null
            if (!usernameInput || !passwordInput) {
                console.error("Elemen input tidak ditemukan! Cek ID HTML-nya.");
                return;
            }

            statusLabel.innerText = "Mencoba login...";
            statusLabel.style.color = "orange";

            try {
                const response = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        username: usernameInput.value,
                        password: passwordInput.value
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    localStorage.setItem('embun_token', data.access_token);
                    statusLabel.innerText = `Sukses! Login sebagai: ${data.user.role}`;
                    statusLabel.style.color = "blue";
                    loadDropdownData();
                } else {
                    statusLabel.innerText = `Gagal: ${data.message}`;
                    statusLabel.style.color = "red";
                }
            } catch (error) {
                console.error("Login Error:", error);
                statusLabel.innerText = "Error jaringan!";
                statusLabel.style.color = "red";
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
        // 5. Fungsi Buat Pesanan (Guest)
        async function testBuatPesanan() {
            const status = document.getElementById('order-status');
            status.innerText = "Memproses pesanan...";
            status.style.color = "orange";

            // Bikin array items sesuai format controller
            const payload = {
                customer_name: document.getElementById('order_name').value,
                table_number: document.getElementById('order_table').value,
                items: [
                    {
                        menu_id: document.getElementById('order_menu_id').value,
                        quantity: document.getElementById('order_qty').value
                    }
                ]
            };

            try {
                const response = await fetch(`${API_URL}/orders`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (response.ok) {
                    status.innerText = `Sukses: ${data.message} (Total: Rp ${data.total_price})`;
                    status.style.color = "green";
                } else {
                    status.innerText = `Gagal: Cek inputanmu!`;
                    status.style.color = "red";
                }
            } catch (error) {
                status.innerText = "Error jaringan!";
            }
        }

        // 6. Fungsi Update Pesanan (Staff)
        async function testUpdatePesanan() {
            const token = localStorage.getItem('embun_token');
            const statusLabel = document.getElementById('verif-order-status');
            const id = document.getElementById('verif_order_id').value;
            const newStatus = document.getElementById('verif_order_status').value;
            
            if (!token) return statusLabel.innerText = "Ditolak: Belum login!";
            statusLabel.innerText = "Memproses...";

            try {
                const response = await fetch(`${API_URL}/orders/${id}/status`, {
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
            }
        }

        // 7. Fungsi Tambah Stok (Admin/Staff)
        async function testTambahStok() {
            const token = localStorage.getItem('embun_token');
            const statusLabel = document.getElementById('inv-status');
            
            if (!token) {
                statusLabel.innerText = "Ditolak: Belum login!";
                statusLabel.style.color = "red";
                return;
            }

            statusLabel.innerText = "Menyimpan bahan...";
            statusLabel.style.color = "orange";

            const payload = {
                item_name: document.getElementById('inv_name').value,
                quantity: document.getElementById('inv_qty').value,
                unit: document.getElementById('inv_unit').value
            };

            try {
                const response = await fetch(`${API_URL}/inventory`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (response.ok) {
                    statusLabel.innerText = `Sukses: ${data.message}`;
                    statusLabel.style.color = "green";
                } else {
                    statusLabel.innerText = `Gagal: Cek inputanmu!`;
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
