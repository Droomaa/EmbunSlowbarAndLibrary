<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir - Embun Cafe</title>
    <style>
        body { margin: 0; font-family: sans-serif; background-color: #f8f9f6; color: #333; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
        
        /* Top Navigation */
        .top-nav { background: #f4f3ed; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; }
        .nav-links a { text-decoration: none; color: #666; font-weight: bold; margin-right: 20px; padding-bottom: 18px; }
        .nav-links a.active { color: #4c7c5f; border-bottom: 3px solid #4c7c5f; }
        
        /* Layout 3 Kolom */
        .pos-container { display: flex; flex: 1; overflow: hidden; }
        
        /* Kolom Kiri: Katalog */
        .col-catalog { flex: 4; padding: 20px; overflow-y: auto; }
        .category-pills button { background: white; border: 1px solid #ddd; padding: 8px 16px; border-radius: 20px; margin-right: 10px; cursor: pointer; color: #555; transition: 0.2s; }
        .category-pills button.active, .category-pills button:hover { background: #4c7c5f; color: white; border-color: #4c7c5f; }
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 20px; }
        .product-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: relative; transition: 0.2s; }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .product-img { height: 120px; background: #e2e8e4; width: 100%; display: flex; align-items: center; justify-content: center; font-size: 30px; color: #4c7c5f; opacity: 0.5; }
        .status-badge { position: absolute; top: 10px; right: 10px; background: #e6f4ea; color: #1e8e3e; font-size: 10px; padding: 4px 8px; border-radius: 10px; font-weight: bold; }
        
        /* Kolom Tengah: Cart */
        .col-cart { flex: 3; background: #fdfdfa; border-left: 1px solid #eee; border-right: 1px solid #eee; display: flex; flex-direction: column; }
        .cart-items { flex: 1; overflow-y: auto; padding: 20px; }
        .cart-item { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #eee; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .qty-btn { background: #eee; border: none; width: 25px; height: 25px; border-radius: 50%; cursor: pointer; font-weight: bold; display: flex; align-items: center; justify-content: center; }
        .qty-btn:hover { background: #ddd; }
        .cart-summary { background: #e8ece9; padding: 20px; }
        
        /* Kolom Kanan: Checkout */
        .col-checkout { flex: 3; padding: 20px; background: #fdfdfa; overflow-y: auto; }
        .payment-btn { flex: 1; padding: 15px 0; background: white; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; font-weight: bold; color: #555; transition: 0.2s; }
        .payment-btn.active { border: 2px solid #4c7c5f; color: #4c7c5f; background: #f5fbf7; }
        .btn-action { width: 100%; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-bottom: 10px; transition: 0.2s; }
        .btn-action:hover { opacity: 0.9; }
        .btn-action:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>

    <div class="top-nav">
        <div style="display: flex; align-items: center; gap: 40px;">
            <h2 style="margin: 0; color: #4c7c5f;">☕ Embun Cafe</h2>
            <div class="nav-links">
                <a href="#" class="active">Cashier</a>
                <a href="/karyawan/online">Orders</a>
                <a href="/karyawan/stok">Inventory</a>
                <a href="/karyawan/dashboard">Dashboard</a>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <input type="text" placeholder="🔍 Cari menu..." style="padding: 8px 15px; border-radius: 20px; border: 1px solid #ddd; width: 200px;">
            <div style="text-align: right; line-height: 1.2;">
                <strong style="font-size: 14px; display: block;">Andi - Barista</strong>
                <span style="font-size: 11px; color: #888;">Shift Pagi</span>
            </div>
        </div>
    </div>

    <div class="pos-container">
        
        <div class="col-catalog">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; color: #4c7c5f;">Katalog Menu</h3>
                <span style="font-size: 12px; color: #888;" id="current-time">Hari ini</span>
            </div>
            <div class="category-pills">
                <button class="active">All Items</button>
                <button>Coffee</button>
                <button>Non-Coffee</button>
                <button>Food</button>
            </div>
            
            <div class="product-grid" id="product-grid">
                <p style="color: #888; text-align: center; grid-column: span 3;">⏳ Memuat menu dari server...</p>
            </div>
        </div>

        <div class="col-cart">
            <div style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #4c7c5f;">Pesanan Aktif</h3>
                <button onclick="clearCart()" style="background: transparent; border: none; color: #dc3545; font-size: 12px; font-weight: bold; cursor: pointer;">🗑️ Kosongkan</button>
            </div>
            
            <div class="cart-items" id="cart-items">
                <div style="text-align: center; color: #aaa; margin-top: 50px;">Keranjang masih kosong</div>
            </div>

            <div class="cart-summary">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #555;">
                    <span>Subtotal</span><span id="val-subtotal">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #555; border-bottom: 1px solid #ccc; padding-bottom: 15px;">
                    <span>Tax (PB1 10%)</span><span id="val-tax">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 18px;">
                    <strong style="color: #4c7c5f;">Total Pembayaran</strong><strong style="color: #4c7c5f;" id="val-total">Rp 0</strong>
                </div>
            </div>
        </div>

        <div class="col-checkout">
            <h3 style="margin: 0 0 20px 0; color: #856404; font-weight: normal; font-family: serif; font-size: 22px;">Detail Pesanan</h3>
            
            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="font-size: 10px; font-weight: bold; color: #888;">NAMA PELANGGAN</label>
                    <input type="text" id="input-customer" placeholder="Misal: Kak Rara" style="width: 100%; box-sizing: border-box; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ddd;">
                </div>
            </div>

            <h4 style="margin: 0 0 10px 0; color: #856404; font-weight: normal; font-family: serif;">Metode Pembayaran</h4>
            <div style="display: flex; gap: 10px; margin-bottom: 25px;">
                <button class="payment-btn active">💵 TUNAI</button>
                <button class="payment-btn">📱 QRIS</button>
                <button class="payment-btn">💳 DEBIT</button>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="font-size: 10px; font-weight: bold; color: #888;">UANG DITERIMA (TUNAI)</label>
                <input type="number" id="input-cash" placeholder="0" style="width: 100%; box-sizing: border-box; padding: 15px; font-size: 24px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; background: #fffaf0;">
                
                <div style="display: flex; justify-content: space-between; margin-top: 15px; padding: 15px; background: #f4f3ed; border-radius: 8px;">
                    <span style="color: #666;">Kembalian</span><strong style="color: #856404; font-size: 18px;" id="val-change">Rp 0</strong>
                </div>
            </div>

            <button id="btn-checkout" onclick="processCheckout()" class="btn-action" style="background: #4c7c5f; color: white; border: none;" disabled>💾 Simpan Transaksi</button>
            <button class="btn-action" style="background: transparent; color: #4c7c5f; border: 1px solid #4c7c5f;">🖨️ Cetak Struk</button>
        </div>
    </div>

    <script>
        const API_URL = 'http://127.0.0.1:8000/api';
        const token = localStorage.getItem('embun_token');
        
        let menus = [];
        let cart = [];
        let summary = { subtotal: 0, tax: 0, total: 0 };

        // Utility Format Rupiah
        const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);

        // 1. Fetch Katalog Menu saat Halaman Dimuat
        document.addEventListener('DOMContentLoaded', async () => {
            // Set Jam
            document.getElementById('current-time').innerText = new Date().toLocaleString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

            try {
                const res = await fetch(`${API_URL}/karyawan/pos/menus`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const result = await res.json();
                if(res.ok) {
                    menus = result.data;
                    renderCatalog();
                }
            } catch (error) {
                console.error("Gagal memuat menu:", error);
                document.getElementById('product-grid').innerHTML = '<p style="color:red; grid-column: span 3;">Gagal terhubung ke server.</p>';
            }

            // Event Listener Kalkulator Kembalian
            document.getElementById('input-cash').addEventListener('input', calculateChange);
        });

        // 2. Render Katalog ke Layar
        function renderCatalog() {
            const grid = document.getElementById('product-grid');
            grid.innerHTML = '';

            menus.forEach(menu => {
                // Handle variasi nama kolom database (menuName atau name, price atau harga)
                const id = menu.id || menu.menu_id;
                const name = menu.menuName || menu.name || 'Menu Baru';
                const price = parseFloat(menu.price || menu.harga || 0);

                grid.innerHTML += `
                    <div class="product-card">
                        <span class="status-badge">Tersedia</span>
                        <div class="product-img">🍔</div>
                        <div style="padding: 15px;">
                            <strong style="display: block; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${name}">${name}</strong>
                            <span style="color: #888; font-size: 13px;">${formatRupiah(price)}</span>
                            <button onclick="addToCart(${id})" style="width: 100%; padding: 8px; margin-top: 10px; background: #f4f3ed; border: none; border-radius: 6px; color: #4c7c5f; font-weight: bold; cursor: pointer;">+ Tambah</button>
                        </div>
                    </div>
                `;
            });
        }

        // 3. Logika Keranjang (Tambah, Kurang, Hapus)
        function addToCart(menuId) {
            const menu = menus.find(m => (m.id || m.menu_id) === menuId);
            if(!menu) return;

            const price = parseFloat(menu.price || menu.harga || 0);
            const name = menu.menuName || menu.name || 'Menu';

            // Cek apakah item sudah ada di keranjang
            const existingItem = cart.find(item => item.menu_id === menuId);
            if(existingItem) {
                existingItem.quantity += 1;
                existingItem.subtotal = existingItem.quantity * existingItem.price;
            } else {
                cart.push({
                    menu_id: menuId,
                    name: name,
                    price: price,
                    quantity: 1,
                    subtotal: price
                });
            }
            renderCart();
        }

        function updateQty(menuId, delta) {
            const itemIndex = cart.findIndex(item => item.menu_id === menuId);
            if(itemIndex > -1) {
                cart[itemIndex].quantity += delta;
                if(cart[itemIndex].quantity <= 0) {
                    cart.splice(itemIndex, 1); // Hapus jika qty 0
                } else {
                    cart[itemIndex].subtotal = cart[itemIndex].quantity * cart[itemIndex].price;
                }
                renderCart();
            }
        }

        function clearCart() {
            cart = [];
            document.getElementById('input-customer').value = '';
            document.getElementById('input-cash').value = '';
            renderCart();
        }

        // 4. Render Keranjang & Hitung Total
        function renderCart() {
            const cartContainer = document.getElementById('cart-items');
            const btnCheckout = document.getElementById('btn-checkout');
            
            if(cart.length === 0) {
                cartContainer.innerHTML = '<div style="text-align: center; color: #aaa; margin-top: 50px;">Keranjang masih kosong</div>';
                summary = { subtotal: 0, tax: 0, total: 0 };
                btnCheckout.disabled = true;
            } else {
                cartContainer.innerHTML = '';
                summary.subtotal = 0;

                cart.forEach(item => {
                    summary.subtotal += item.subtotal;
                    cartContainer.innerHTML += `
                        <div class="cart-item">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="flex: 1;">
                                    <strong style="display: block; font-size: 14px;">${item.name}</strong>
                                    <span style="color: #4c7c5f; font-size: 12px;">${formatRupiah(item.price)}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px; background: #f9f9f9; padding: 4px; border-radius: 20px;">
                                    <button onclick="updateQty(${item.menu_id}, -1)" class="qty-btn">-</button>
                                    <span style="font-weight: bold; font-size: 14px; width: 15px; text-align: center;">${item.quantity}</span>
                                    <button onclick="updateQty(${item.menu_id}, 1)" class="qty-btn" style="background: #4c7c5f; color: white;">+</button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Hitung Pajak (10%) & Total
                summary.tax = summary.subtotal * 0.10;
                summary.total = summary.subtotal + summary.tax;
                btnCheckout.disabled = false;
            }

            // Update DOM Teks Total
            document.getElementById('val-subtotal').innerText = formatRupiah(summary.subtotal);
            document.getElementById('val-tax').innerText = formatRupiah(summary.tax);
            document.getElementById('val-total').innerText = formatRupiah(summary.total);
            
            calculateChange(); // Update kembalian setiap kali keranjang berubah
        }

        // 5. Kalkulator Kembalian
        function calculateChange() {
            const cashInput = document.getElementById('input-cash').value;
            const cash = parseFloat(cashInput) || 0;
            const change = cash - summary.total;
            
            const changeEl = document.getElementById('val-change');
            if (change < 0) {
                changeEl.innerText = "Uang Kurang!";
                changeEl.style.color = "#dc3545";
            } else {
                changeEl.innerText = formatRupiah(change);
                changeEl.style.color = "#856404";
            }
        }

        // 6. Tembak Data ke Server (Checkout)
        async function processCheckout() {
            if(cart.length === 0) return;

            const btnCheckout = document.getElementById('btn-checkout');
            btnCheckout.disabled = true;
            btnCheckout.innerText = "⏳ Menyimpan...";

            const payload = {
                customer_name: document.getElementById('input-customer').value || 'Walk-in Customer',
                total_price: summary.total,
                items: cart.map(c => ({
                    menu_id: c.menu_id,
                    quantity: c.quantity,
                    subtotal: c.subtotal
                }))
            };

            try {
                const res = await fetch(`${API_URL}/karyawan/pos/checkout`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();

                if(res.ok) {
                    alert('🎉 TRANSAKSI BERHASIL!\nOrder ID: EB-' + result.order_id);
                    clearCart();
                } else {
                    alert('❌ Gagal: ' + (result.message || 'Terjadi kesalahan server'));
                }
            } catch (error) {
                console.error("Gagal checkout:", error);
                alert('❌ Gagal terhubung ke server saat checkout.');
            } finally {
                btnCheckout.innerText = "💾 Simpan Transaksi";
            }
        }
    </script>
</body>
</html>
