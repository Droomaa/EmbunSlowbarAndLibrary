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
        
        /* 3 Columns Layout */
        .pos-container { display: flex; flex: 1; overflow: hidden; }
        
        /* Kolom Kiri: Katalog */
        .col-catalog { flex: 4; padding: 20px; overflow-y: auto; }
        .category-pills button { background: white; border: 1px solid #ddd; padding: 8px 16px; border-radius: 20px; margin-right: 10px; cursor: pointer; color: #555; }
        .category-pills button.active { background: #4c7c5f; color: white; border: none; }
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 20px; }
        .product-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: relative; }
        .product-img { height: 120px; background: #ddd; width: 100%; }
        .status-badge { position: absolute; top: 10px; right: 10px; background: #e6f4ea; color: #1e8e3e; font-size: 10px; padding: 4px 8px; border-radius: 10px; font-weight: bold; }
        
        /* Kolom Tengah: Cart */
        .col-cart { flex: 3; background: #fdfdfa; border-left: 1px solid #eee; border-right: 1px solid #eee; display: flex; flex-direction: column; }
        .cart-items { flex: 1; overflow-y: auto; padding: 20px; }
        .cart-item { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #eee; }
        .qty-btn { background: #eee; border: none; width: 25px; height: 25px; border-radius: 50%; cursor: pointer; font-weight: bold; }
        .cart-summary { background: #e8ece9; padding: 20px; }
        
        /* Kolom Kanan: Checkout */
        .col-checkout { flex: 3; padding: 20px; background: #fdfdfa; overflow-y: auto; }
        .payment-btn { flex: 1; padding: 15px 0; background: white; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; font-weight: bold; color: #555; }
        .payment-btn.active { border: 2px solid #4c7c5f; color: #4c7c5f; background: #f5fbf7; }
        .btn-action { width: 100%; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="top-nav">
        <div style="display: flex; align-items: center; gap: 40px;">
            <h2 style="margin: 0; color: #4c7c5f;">Embun Cafe</h2>
            <div class="nav-links">
                <a href="#" class="active">Cashier</a>
                <a href="/karyawan/online">Orders</a>
                <a href="/karyawan/stok">Inventory</a>
                <a href="/admin/penjualan">Reports</a>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <input type="text" placeholder="🔍 Cari menu..." style="padding: 8px 15px; border-radius: 20px; border: 1px solid #ddd; width: 200px;">
            <div style="text-align: right; line-height: 1.2;">
                <strong style="font-size: 14px; display: block;">Andi - Senior Barista</strong>
                <span style="font-size: 11px; color: #888;">Shift Pagi</span>
            </div>
            <button style="background: #4c7c5f; color: white; border: none; padding: 8px 15px; border-radius: 20px; font-weight: bold;">Shift End</button>
        </div>
    </div>

    <div class="pos-container">
        
        <div class="col-catalog">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; color: #4c7c5f;">Katalog Menu</h3>
                <span style="font-size: 12px; color: #888;">Senin, 24 Okt 2023 | 14:45</span>
            </div>
            <div class="category-pills">
                <button class="active">All Items</button>
                <button>Coffee</button>
                <button>Non-Coffee</button>
                <button>Food</button>
            </div>
            <div class="product-grid">
                <div class="product-card">
                    <span class="status-badge">Tersedia</span>
                    <div class="product-img"></div>
                    <div style="padding: 15px;">
                        <strong style="display: block; margin-bottom: 5px;">Aren Palm Latte</strong>
                        <span style="color: #888; font-size: 13px;">Rp 28.000</span>
                        <button style="width: 100%; padding: 8px; margin-top: 10px; background: #f4f3ed; border: none; border-radius: 6px; color: #4c7c5f; font-weight: bold; cursor: pointer;">+ Tambah</button>
                    </div>
                </div>
                <div class="product-card">
                    <span class="status-badge" style="background: #fce8e6; color: #dc3545;">Habis</span>
                    <div class="product-img"></div>
                    <div style="padding: 15px; opacity: 0.6;">
                        <strong style="display: block; margin-bottom: 5px;">Cold Brew Classic</strong>
                        <span style="color: #888; font-size: 13px;">Rp 32.000</span>
                        <button style="width: 100%; padding: 8px; margin-top: 10px; background: #eee; border: none; border-radius: 6px; color: #888; cursor: not-allowed;" disabled>Stok Kosong</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-cart">
            <div style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #4c7c5f;">Pesanan Aktif</h3>
                <button style="background: transparent; border: none; color: #dc3545; font-size: 12px; font-weight: bold; cursor: pointer;">🗑️ Kosongkan</button>
            </div>
            <div class="cart-items">
                <div class="cart-item">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div>
                            <strong style="display: block;">Aren Palm Latte</strong>
                            <span style="color: #4c7c5f; font-size: 12px;">Rp 28.000</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; background: #f9f9f9; padding: 4px; border-radius: 20px;">
                            <button class="qty-btn">-</button>
                            <span style="font-weight: bold;">1</span>
                            <button class="qty-btn" style="background: #4c7c5f; color: white;">+</button>
                        </div>
                    </div>
                    <input type="text" placeholder="Catatan: Less ice..." style="width: 100%; padding: 6px; border: none; background: #f9f9f9; border-radius: 4px; font-size: 12px;">
                </div>
            </div>
            <div class="cart-summary">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #555;">
                    <span>Subtotal</span><span>Rp 107.000</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #555; border-bottom: 1px solid #ccc; padding-bottom: 15px;">
                    <span>Tax (PB1 10%)</span><span>Rp 10.700</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 18px;">
                    <strong style="color: #4c7c5f;">Total Pembayaran</strong><strong style="color: #4c7c5f;">Rp 117.700</strong>
                </div>
            </div>
        </div>

        <div class="col-checkout">
            <h3 style="margin: 0 0 20px 0; color: #856404; font-weight: normal; font-family: serif; font-size: 22px;">Detail Pesanan</h3>
            
            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="font-size: 10px; font-weight: bold; color: #888;">TIPE PESANAN</label>
                    <div style="display: flex; background: #eee; border-radius: 6px; padding: 3px; margin-top: 5px;">
                        <button style="flex: 1; border: none; background: #856404; color: white; padding: 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Dine In</button>
                        <button style="flex: 1; border: none; background: transparent; padding: 8px; font-size: 12px;">Take Away</button>
                    </div>
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 10px; font-weight: bold; color: #888;">NO. MEJA</label>
                    <select style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ddd; background: white;">
                        <option>Meja 03</option>
                    </select>
                </div>
            </div>

            <h4 style="margin: 0 0 10px 0; color: #856404; font-weight: normal; font-family: serif;">Metode Pembayaran</h4>
            <div style="display: flex; gap: 10px; margin-bottom: 25px;">
                <button class="payment-btn active">💵 TUNAI</button>
                <button class="payment-btn">📱 QRIS</button>
                <button class="payment-btn">💳 DEBIT</button>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="font-size: 10px; font-weight: bold; color: #888;">JUMLAH DIBAYAR (TUNAI)</label>
                <input type="text" value="Rp 120.000" style="width: 100%; box-sizing: border-box; padding: 15px; font-size: 24px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; background: #fffaf0;">
                
                <div style="display: flex; justify-content: space-between; margin-top: 15px; padding: 15px; background: #f4f3ed; border-radius: 8px;">
                    <span style="color: #666;">Kembalian</span><strong style="color: #856404; font-size: 18px;">Rp 2.300</strong>
                </div>
            </div>

            <button class="btn-action" style="background: #4c7c5f; color: white; border: none;">💾 Simpan Transaksi</button>
            <button class="btn-action" style="background: transparent; color: #4c7c5f; border: 1px solid #4c7c5f;">🖨️ Cetak Struk</button>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="#" style="color: #888; font-size: 12px; text-decoration: none;">🕒 Lihat Riwayat Transaksi Hari Ini</a>
            </div>
        </div>

    </div>
</body>
</html>
