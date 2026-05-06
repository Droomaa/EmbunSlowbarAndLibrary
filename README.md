# ☕ Embun Cafe — Database Documentation

Repositori ini berisi struktur database backend untuk aplikasi **Embun Cafe**, yang dibangun menggunakan **Laravel** dengan database **SQLite**. Database ini mencakup fitur manajemen pesanan, reservasi meja, inventaris, menu, pelanggan, hingga laporan.

---

## 🛠️ Teknologi yang Digunakan

| Teknologi   | Versi        |
|-------------|--------------|
| PHP         | >= 8.2       |
| Laravel     | >= 11.x      |
| Database    | SQLite       |
| ORM         | Eloquent     |

---

## 🗄️ Struktur Database

Database terdiri dari **8 tabel utama** yang saling berelasi sebagai berikut:

### 1. `users`
Tabel untuk menyimpan data pengguna sistem (admin/kasir).

| Kolom       | Tipe      | Keterangan                    |
|-------------|-----------|-------------------------------|
| `id`        | bigint    | Primary Key                   |
| `name`      | string    | Nama pengguna                 |
| `email`     | string    | Email (unik)                  |
| `password`  | string    | Password (ter-hash)           |
| `timestamps`| timestamp | `created_at` & `updated_at`   |

---

### 2. `customers`
Tabel untuk menyimpan data pelanggan yang melakukan reservasi.

| Kolom       | Tipe      | Keterangan                    |
|-------------|-----------|-------------------------------|
| `id`        | bigint    | Primary Key                   |
| `name`      | string    | Nama pelanggan                |
| `noHP`      | string    | Nomor HP (opsional)           |
| `timestamps`| timestamp | `created_at` & `updated_at`   |

---

### 3. `menus`
Tabel untuk menyimpan daftar menu makanan dan minuman yang tersedia.

| Kolom         | Tipe      | Keterangan                    |
|---------------|-----------|-------------------------------|
| `id`          | bigint    | Primary Key                   |
| `menuName`    | string    | Nama menu                     |
| `price`       | float     | Harga menu                    |
| `description` | text      | Deskripsi menu (opsional)     |
| `timestamps`  | timestamp | `created_at` & `updated_at`   |

---

### 4. `inventaris`
Tabel untuk menyimpan data stok bahan baku atau peralatan cafe.

| Kolom       | Tipe      | Keterangan                         |
|-------------|-----------|------------------------------------|
| `id`        | bigint    | Primary Key                        |
| `itemName`  | string    | Nama item/bahan                    |
| `stock`     | integer   | Jumlah stok (default: 0)           |
| `satuan`    | string    | Satuan stok (contoh: kg, liter)    |
| `timestamps`| timestamp | `created_at` & `updated_at`        |

---

### 5. `orders`
Tabel untuk mencatat transaksi pesanan yang dibuat oleh pengguna (kasir).

| Kolom        | Tipe      | Keterangan                              |
|--------------|-----------|-----------------------------------------|
| `id`         | bigint    | Primary Key                             |
| `user_id`    | bigint    | Foreign Key → `users.id` (cascade)      |
| `date`       | date      | Tanggal transaksi                       |
| `totalOrder` | float     | Total harga keseluruhan pesanan         |
| `status`     | string    | Status pesanan (contoh: Pending, Done)  |
| `timestamps` | timestamp | `created_at` & `updated_at`            |

---

### 6. `order_details`
Tabel pivot yang menyimpan detail item dari setiap pesanan.

| Kolom       | Tipe      | Keterangan                              |
|-------------|-----------|-----------------------------------------|
| `id`        | bigint    | Primary Key                             |
| `order_id`  | bigint    | Foreign Key → `orders.id` (cascade)     |
| `menu_id`   | bigint    | Foreign Key → `menus.id` (cascade)      |
| `quantity`  | integer   | Jumlah item yang dipesan                |
| `subtotal`  | float     | Subtotal harga (quantity × price)       |
| `timestamps`| timestamp | `created_at` & `updated_at`            |

---

### 7. `reservations`
Tabel untuk menyimpan data pemesanan/booking meja oleh pelanggan.

| Kolom         | Tipe      | Keterangan                                    |
|---------------|-----------|-----------------------------------------------|
| `id`          | bigint    | Primary Key                                   |
| `customer_id` | bigint    | Foreign Key → `customers.id` (cascade)        |
| `date`        | date      | Tanggal booking                               |
| `startTime`   | time      | Jam mulai reservasi                           |
| `duration`    | integer   | Durasi reservasi (dalam jam)                  |
| `jumlahOrang` | integer   | Jumlah orang yang akan datang                 |
| `status`      | string    | Status reservasi (default: `Pending`)         |
| `timestamps`  | timestamp | `created_at` & `updated_at`                   |

> Status reservasi bisa diubah menjadi: `Pending`, `Confirmed`, atau `Cancelled`.

---

### 8. `reports`
Tabel untuk menyimpan laporan periodik cafe (harian, mingguan, bulanan, dll).

| Kolom         | Tipe      | Keterangan                            |
|---------------|-----------|---------------------------------------|
| `id`          | bigint    | Primary Key                           |
| `reportType`  | string    | Jenis laporan (contoh: Sales, Stock)  |
| `date`        | date      | Tanggal laporan                       |
| `periode`     | string    | Periode laporan (contoh: Mei 2026)    |
| `description` | text      | Keterangan tambahan (opsional)        |
| `timestamps`  | timestamp | `created_at` & `updated_at`           |

---

## 🔗 Relasi Antar Tabel

```
users ──────────────────── orders (1 user bisa punya banyak orders)
                               │
                               └──── order_details ──── menus
                                     (1 order bisa punya banyak item menu)

customers ──────────────── reservations
                           (1 customer bisa punya banyak reservasi)
```

---

## 🚀 Cara Menjalankan Migration & Seeder

Pastikan konfigurasi database sudah benar di file `.env`, lalu jalankan perintah berikut:

```bash
# Jalankan semua migration (buat tabel)
php artisan migrate

# Jalankan seeder (isi data contoh)
php artisan db:seed

# Atau keduanya sekaligus (reset + migrate + seed)
php artisan migrate:fresh --seed
```

---

## 👩‍💻 Kontributor

- **Jasmine Apsari** — Database Design & Migration (Branch: `embun-database`)

---

> Project ini merupakan bagian dari sistem manajemen **Embun Slowbar & Library**.
