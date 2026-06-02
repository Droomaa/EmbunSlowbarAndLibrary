<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menu - Embun Cafe</title>
    <style>
        body { margin: 0; font-family: sans-serif; background-color: #f0f2f0; color: #333; }
        .app-container { max-width: 480px; margin: 0 auto; background: white; min-height: 100vh; position: relative; padding-bottom: 80px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        
        /* Modal Overlay (Gelap di belakang) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 999; display: flex; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
        .modal-box { background: white; padding: 30px; border-radius: 16px; width: 85%; max-width: 320px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        
        /* Input Data */
        .input-field { width: 100%; padding: 12px; margin-top: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; text-align: center; background: #fdfdfa; }
        .btn-primary { background: #4c7c5f; color: white; width: 100%; padding: 14px; border: none; border-radius: 8px; font-weight: bold; font-size: 15px; cursor: pointer; margin-top: 10px; transition: 0.2s; }
        .btn-primary:active { transform: scale(0.98); background: #3b6349; }
        
        /* Header & Menu */
        .header { background: #4c7c5f; color: white; padding: 20px; text-align: center; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; position: sticky; top: 0; z-index: 10; }
        .categories { display: flex; gap: 10px; padding: 20px 15px 10px 15px; overflow-x: auto; white-space: nowrap; }
        .cat-btn { padding: 8px 16px; border-radius: 20px; border: 1px solid #ddd; background: white; font-size: 12px; font-weight: bold; color: #666; cursor: pointer; }
        .cat-btn.active { background: #4c7c5f; color: white; border-color: #4c7c5f; }
        
        .menu-list { padding: 15px; }
        .menu-item { display: flex; gap: 15px; padding: 15px; border: 1px solid #eee; border-radius: 12px; margin-bottom: 15px; }
        .menu-img { width: 80px; height: 80px; background: #e2e8e4; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #4c7c5f; opacity: 0.5; overflow: hidden; }
        .menu-info { flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .menu-name { font-weight: bold; font-size: 15px; margin: 0 0 5px 0; }
        .menu-price { color: #4c7c5f; font-size: 14px; font-weight: bold; margin: 0; }
        
        /* Tombol Cart Dinamis */
        .cart-controls { align-self: flex-start; margin-top: 10px; display: flex; align-items: center; gap: 10px; }
        .add-btn { background: #f4f3ed; color: #4c7c5f; border: none; padding: 8px 15px; border-radius: 20px; font-weight: bold; cursor: pointer; font-size: 12px; }
        .qty-btn { background: #4c7c5f; color: white; border: none; width: 25px; height: 25px; border-radius: 50%; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; }

        /* Floating Cart */
        .floating-cart { position: fixed; bottom: 0; left: 0; width: 100%; display: flex; justify-content: center; pointer-events: none; z-index: 100; }
        .cart-box { background: #4c7c5f; color: white; width: 100%; max-width: 440px; margin: 15px; padding: 15px 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; pointer-events: auto; transform: translateY(100px); transition: 0.3s; }
        .cart-box.show { transform: translateY(0); }
        .checkout-btn { background: white; color: #4c7c5f; border: none; padding: 8px 20px; border-radius: 20px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

    <div class="app-container">
        <div class="header">
            <h1 style="margin: 0; font-size: 20px;">Embun Cafe</h1>
            <p style="margin: 5px 0 0 0; font-size: 12px; opacity: 0.8;" id="header-user">Silakan pilih menu favoritmu!</p>
        </div>

        <div class="categories">
            <button class="cat-btn active">Semua Menu</button>
            <button class="cat-btn">Coffee</button>
            <button class="cat-btn">Non-Coffee</button>
        </div>

        <div class="menu-list" id="menu-list">
            <div style="text-align:center; padding: 50px; color:#888;">⏳ Memuat menu lezat kami...</div>
        </div>

        <div class="floating-cart">
            <div class="cart-box" id="cart-box">
                <div>
                    <div style="font-size: 12px; opacity: 0.8;" id="cart-qty">0 item</div>
                    <div style="font-size: 16px; font-weight: bold;" id="cart-total">Rp 0</div>
                </div>
                <button class="checkout-btn" onclick="tampilkanFormPemesan()">Lanjut Pesan ➔</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="form-modal" style="display: none;">
        <div class="modal-box">
            <h2 style="color: #4c7c5f; margin: 0 0 5px 0;">Data Pesanan</h2>
            <p style="color: #666; font-size: 13px; margin-bottom: 25px;">Ke mana kami harus mengantarkan pesanan ini?</p>
            
            <input type="text" id="input-nama" class="input-field" placeholder="Nama Kamu (mis: Rara)" required>
            
            <select id="input-tipe" class="input-field" style="font-weight: bold; color: #4c7c5f;" onchange="toggleMejaInput()">
                <option value="Dine In">🍽️ Makan di Tempat (Dine In)</option>
                <option value="Takeaway">🥡 Bawa Pulang (Takeaway)</option>
                <option value="Delivery">🛵 Delivery / Pick Up</option>
            </select>

            <input type="number" id="input-meja" class="input-field" placeholder="Nomor Meja (mis: 4)" required>
            
            <button class="btn-primary" onclick="validasiLanjutQris()">Lanjut Pembayaran</button>
            <button style="background: transparent; border: none; color: #888; font-size: 12px; margin-top: 15px; cursor: pointer;" onclick="tutupForm()">← Kembali ke Menu</button>
        </div>
    </div>

    <div class="modal-overlay" id="qris-modal" style="display: none;">
        <div class="modal-box">
            <h3 style="margin: 0 0 10px 0; color: #333;">Total: <span id="qris-total" style="color: #4c7c5f;">Rp 0</span></h3>
            <p style="color: #666; font-size: 12px; margin-bottom: 20px;">Silakan scan QRIS di bawah ini dengan M-Banking atau E-Wallet (Gopay/Ovo/Dana).</p>
            
            <div style="background: #eee; width: 200px; height: 200px; margin: 0 auto 20px auto; display: flex; align-items: center; justify-content: center; border-radius: 12px; border: 2px dashed #ccc;">
                <span style="font-size: 30px; font-weight: bold; color: #aaa;">📱QRIS<br><span style="font-size: 12px;">(Simulasi)</span></span>
            </div>
            
            <button class="btn-primary" id="btn-konfirmasi-bayar" onclick="kirimPesananKeDapur()">Selesai Bayar & Pesan</button>
            <button style="background: transparent; border: none; color: #888; font-size: 12px; margin-top: 15px; cursor: pointer;" onclick="batalQris()">← Edit Data / Menu</button>
        </div>
    </div>

    <script>
        const API_URL = 'http://127.0.0.1:8000/api';
        let menus = [], cart = [], subtotal = 0, grandTotal = 0;
        let customerData = { nama: '', meja: '', tipe: '' };

        const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);

        // 1. Fetch Data Menu
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const res = await fetch(`${API_URL}/karyawan/pos/menus`, { headers: { 'Accept': 'application/json' } });
                const result = await res.json();
                if(res.ok) { 
                    menus = result.data; 
                    renderMenus(); 
                }
            } catch (error) {
                document.getElementById('menu-list').innerHTML = '<div style="color:red; text-align:center;">❌ Gagal memuat menu. Cek koneksi Anda.</div>';
            }
        });

        // 2. Render Menu
        function renderMenus() {
            const list = document.getElementById('menu-list');
            list.innerHTML = '';
            
            menus.forEach(menu => {
                const id = menu.id || menu.menu_id;
                const name = menu.menuName || menu.name || 'Menu Baru';
                const price = parseFloat(menu.price || menu.harga || 0);

                const imageContent = menu.image
                    ? `<img src="/storage/${menu.image}" style="width: 100%; height: 100%; object-fit: cover;">` 
                    : `☕`;

                const cartItem = cart.find(c => c.menu_id === id);
                let cartControlsHTML = '';
                
                if (cartItem && cartItem.quantity > 0) {
                    cartControlsHTML = `
                        <div class="cart-controls">
                            <button class="qty-btn" onclick="kurangiDariKeranjang(${id})">-</button>
                            <span style="font-weight: bold; width: 20px; text-align: center;">${cartItem.quantity}</span>
                            <button class="qty-btn" onclick="tambahKeKeranjang(${id})">+</button>
                        </div>
                    `;
                } else {
                    cartControlsHTML = `<button class="add-btn" onclick="tambahKeKeranjang(${id})">+ Tambah</button>`;
                }

                list.innerHTML += `
                    <div class="menu-item">
                        <div class="menu-img" style="${menu.image ? 'background: transparent; padding: 0;' : ''}">
                            ${imageContent}
                        </div>
                        <div class="menu-info">
                            <p class="menu-name">${name}</p>
                            <p class="menu-price">${formatRupiah(price)}</p>
                            ${cartControlsHTML}
                        </div>
                    </div>
                `;
            });
        }

        // 3. Logika Keranjang
        window.tambahKeKeranjang = function(menuId) {
            const menu = menus.find(m => (m.id || m.menu_id) === menuId);
            if(!menu) return;

            const price = parseFloat(menu.price || menu.harga || 0);
            const existing = cart.find(c => c.menu_id === menuId);
            
            if(existing) {
                existing.quantity += 1;
                existing.subtotal = existing.quantity * price;
            } else {
                cart.push({ menu_id: menuId, quantity: 1, subtotal: price });
            }
            
            updateKeranjangUI();
            renderMenus(); 
        }

        window.kurangiDariKeranjang = function(menuId) {
            const index = cart.findIndex(c => c.menu_id === menuId);
            if (index !== -1) {
                const menu = menus.find(m => (m.id || m.menu_id) === menuId);
                const price = parseFloat(menu.price || menu.harga || 0);

                if (cart[index].quantity > 1) {
                    cart[index].quantity -= 1;
                    cart[index].subtotal = cart[index].quantity * price;
                } else {
                    cart.splice(index, 1);
                }
            }
            updateKeranjangUI();
            renderMenus();
        }

        function updateKeranjangUI() {
            const cartBox = document.getElementById('cart-box');
            if(cart.length === 0) { 
                cartBox.classList.remove('show'); 
                return; 
            }

            let totalQty = 0; subtotal = 0;
            cart.forEach(item => { totalQty += item.quantity; subtotal += item.subtotal; });

            grandTotal = subtotal + (subtotal * 0.10);

            document.getElementById('cart-qty').innerText = `${totalQty} item (Inc. Tax)`;
            document.getElementById('cart-total').innerText = formatRupiah(grandTotal);
            
            cartBox.classList.add('show');
        }

        // 4. Modal Alur & Validasi Cerdas
        window.tampilkanFormPemesan = function() {
            document.getElementById('form-modal').style.display = 'flex';
        }
        
        window.tutupForm = function() {
            document.getElementById('form-modal').style.display = 'none';
        }

        // FUNGSI BARU: Sembunyikan Nomor Meja jika bukan Dine In
        window.toggleMejaInput = function() {
            const tipe = document.getElementById('input-tipe').value;
            const inputMeja = document.getElementById('input-meja');
            
            if (tipe === 'Dine In') {
                inputMeja.style.display = 'block';
            } else {
                inputMeja.style.display = 'none';
                inputMeja.value = ''; // Kosongkan nilainya
            }
        }

        window.validasiLanjutQris = function() {
            const nama = document.getElementById('input-nama').value.trim();
            const tipe = document.getElementById('input-tipe').value;
            let meja = document.getElementById('input-meja').value.trim();

            if(!nama) {
                alert("Kak, Nama pemesan harus diisi ya agar pesanan tidak tertukar!");
                return;
            }

            // Validasi khusus Meja jika Dine In
            if (tipe === 'Dine In') {
                if (!meja) {
                    alert("Kak, Nomor Meja harus diisi untuk pesanan Makan di Tempat (Dine In)!");
                    return;
                }
            } else {
                // Jika Takeaway/Delivery, isi meja dengan tipe pesanan agar orang dapur tahu
                meja = tipe;
            }

            customerData = { nama, meja, tipe };
            
            document.getElementById('form-modal').style.display = 'none';
            
            // Tampilan di Header beda tergantung tipe
            if (tipe === 'Dine In') {
                document.getElementById('header-user').innerText = `Meja ${meja} • Kak ${nama}`;
            } else {
                document.getElementById('header-user').innerText = `${tipe} • Kak ${nama}`;
            }
            
            document.getElementById('qris-total').innerText = formatRupiah(grandTotal);
            document.getElementById('qris-modal').style.display = 'flex';
        }

        window.batalQris = function() {
            document.getElementById('qris-modal').style.display = 'none';
            document.getElementById('form-modal').style.display = 'flex'; 
        }

        // 5. Tembak Data ke Dapur
        window.kirimPesananKeDapur = async function() {
            const btnKonfirmasi = document.getElementById('btn-konfirmasi-bayar');
            btnKonfirmasi.innerText = '⏳ Memproses...';
            btnKonfirmasi.disabled = true;

            const payload = {
                customer_name: customerData.nama + " (QR Order)",
                table_number: customerData.meja, // Bisa berupa angka meja, kata "Takeaway", atau "Delivery"
                order_type: customerData.tipe, 
                payment_method: 'QRIS',
                total_price: grandTotal,
                items: cart
            };

            try {
                const res = await fetch(`${API_URL}/karyawan/pos/checkout`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if(res.ok) {
                    const infoMeja = customerData.tipe === 'Dine In' ? `di Meja ${customerData.meja}` : `(Area Pick Up/Delivery)`;
                    alert(`🎉 PEMBAYARAN BERHASIL!\nPesanan sedang disiapkan dapur. Mohon ditunggu ${infoMeja}`);
                    window.location.reload(); 
                } else {
                    alert('❌ Gagal memproses pesanan. Silakan coba lagi.');
                }
            } catch (error) {
                alert('❌ Gagal terhubung ke server kasir.');
            } finally {
                btnKonfirmasi.innerText = 'Selesai Bayar & Pesan';
                btnKonfirmasi.disabled = false;
            }
        }
    </script>
</body>
</html>
