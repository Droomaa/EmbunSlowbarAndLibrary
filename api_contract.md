# Embun Cafe API Contract

Dokumentasi ini berisi daftar endpoint API yang telah diamankan dan disiapkan untuk diintegrasikan dengan Frontend. Semua exception yang terekspos telah ditutup, dan endpoint publik dilengkapi dengan proteksi _rate limiting_ (10 req/min).

## 1. Autentikasi

### Login
- **Method:** `POST`
- **URL:** `/api/login`
- **Auth:** None
- **Body (JSON):**
  ```json
  {
    "username": "owner_andi",
    "password": "password123"
  }
  ```
- **Success Response (200):**
  ```json
  {
    "token": "1|abcdef123...",
    "role": "Owner",
    "user_id": 1,
    "message": "Login successful"
  }
  ```
- **Error Response (401/500):** `Unauthorized` / `Terjadi kesalahan server.`

---

## 2. Akses Publik (Pelanggan)

### Mengambil Daftar Menu
- **Method:** `GET`
- **URL:** `/api/menus`
- **Auth:** None
- **Success Response (200):**
  ```json
  [
    {
      "id": 1,
      "menuName": "Tubruk/Vietnam",
      "price": 8000,
      "description": "Black Coffee",
      "image": null,
      "image_url": null,
      "ingredients": [
        {
          "inventory_id": 1,
          "inventory_name": "Biji Kopi Arabica",
          "quantity_needed": 15
        }
      ]
    }
  ]
  ```

### Mengambil Daftar Add-on
- **Method:** `GET`
- **URL:** `/api/addons`
- **Auth:** None

### Membuat Reservasi Baru (Rate Limited: 10/min)
- **Method:** `POST`
- **URL:** `/api/reservations`
- **Auth:** None
- **Body (JSON):**
  ```json
  {
    "customer_name": "John Doe",
    "phone_number": "08123456789",
    "reservation_date": "2026-06-05 14:00:00",
    "pax": 4,
    "notes": "Minta meja no 2"
  }
  ```
- **Success Response (201):**
  ```json
  {
    "message": "Reservasi berhasil dibuat",
    "reservation_id": 1
  }
  ```

### Membuat Pesanan Baru (Rate Limited: 10/min)
- **Method:** `POST`
- **URL:** `/api/orders`
- **Auth:** None
- **Body (JSON):**
  ```json
  {
    "customer_name": "Jane",
    "table_number": "5",
    "items": [
      {
        "menu_id": 1,
        "quantity": 2
      }
    ]
  }
  ```

---

## 3. Akses Karyawan / Staff

(Perlu menyertakan header `Authorization: Bearer <token>`)

### Kasir Checkout
- **Method:** `POST`
- **URL:** `/api/karyawan/kasir/checkout`
- **Auth:** Bearer Token (Role: Staff)
- **Body (JSON):**
  ```json
  {
    "customer_name": "Budi",
    "table_number": "3",
    "items": [
      { "menu_id": 1, "quantity": 1 }
    ]
  }
  ```

### Update Status Order Online
- **Method:** `PUT`
- **URL:** `/api/karyawan/online/order/{id}/status`
- **Auth:** Bearer Token (Role: Staff)
- **Body (JSON):**
  ```json
  {
    "status": "Processing" // Pending, Processing, Completed
  }
  ```

---

## 4. Akses Admin

(Perlu menyertakan header `Authorization: Bearer <token>`)

### Mendapatkan Laporan Penjualan
- **Method:** `GET`
- **URL:** `/api/admin/penjualan/laporan`
- **Auth:** Bearer Token (Role: Admin)

---

## 5. Akses Owner

(Perlu menyertakan header `Authorization: Bearer <token>`)

### Mendapatkan Data Dashboard
- **Method:** `GET`
- **URL:** `/api/owner/dashboard/data`
- **Auth:** Bearer Token (Role: Owner)
- **Success Response (200):**
  ```json
  {
    "total_revenue": 150000,
    "total_orders": 10,
    "top_menus": [...],
    "low_stock": [...]
  }
  ```

### Menambahkan Menu Baru
- **Method:** `POST`
- **URL:** `/api/owner/menu`
- **Auth:** Bearer Token (Role: Owner)
- **Body (FormData/Multipart):**
  - `menuName` (String)
  - `price` (Number)
  - `description` (String, opsional)
  - `image` (File image, max 2MB, opsional)
  - `ingredients` (JSON String: `[{"inventory_id":1, "quantity_needed":15}]`)

### Update Stok Inventory
- **Method:** `POST`
- **URL:** `/api/owner/inventory`
- **Auth:** Bearer Token (Role: Owner)
- **Body (JSON):**
  ```json
  {
    "item_name": "Biji Kopi Arabica",
    "quantity": 500,
    "unit": "gram"
  }
  ```

---
> **Catatan Frontend:** Semua route yang membutuhkan autentikasi (Owner, Admin, Karyawan) mengharuskan Anda mengirim token dari proses login pada header HTTP request: `Authorization: Bearer <TOKEN>`
