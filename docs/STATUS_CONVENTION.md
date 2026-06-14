# Panduan Status Konvensi — Embun Cafe

Dokumen ini mendefinisikan standar status yang digunakan di backend dan database Embun Cafe. Pastikan semua modul backend dan frontend (Zustand, API) mengacu pada standar ini untuk menjaga konsistensi.

## 1. Entitas: Menu
Tabel: `menus` (kolom `status`)

| Status Backend | Keterangan |
|---|---|
| `Available` | Menu tersedia dan bisa dipesan. |
| `Unavailable` | Menu habis, tidak musim, atau sedang dinonaktifkan. |

## 2. Entitas: Order (Pesanan / Transaksi)
Tabel: `orders` (kolom `status`)

| Status Backend | Keterangan |
|---|---|
| `Pending` | Pesanan baru masuk (biasanya via Online Order). Menunggu konfirmasi kasir/barista. |
| `Processing` | Pesanan sedang disiapkan di dapur/bar. |
| `Completed` | Pesanan selesai, dibayar, stok sudah dipotong. |
| `Cancelled` | Pesanan dibatalkan (baik oleh pelanggan atau kasir). |

> [!NOTE]
> Stok bahan baku (`inventories`) HANYA dipotong ketika status order berubah dari bukan `Completed` menjadi `Completed`.

## 3. Entitas: Reservation (Reservasi Meja)
Tabel: `reservations` (kolom `status`)

| Status Backend | Keterangan |
|---|---|
| `Pending` | Reservasi baru diajukan oleh pelanggan. |
| `Confirmed` | Reservasi disetujui oleh admin/karyawan. |
| `Rejected` | Reservasi ditolak (misal: meja penuh). |

## 4. Entitas: Inventory (Stok Bahan Baku)
Tabel: `inventories` (kolom `quantity`)
> Status stok tidak menggunakan string Enum di database, melainkan dihitung dinamis dari kolom `quantity`.

| Status UI/Kondisi | Ambang Batas (Threshold) |
|---|---|
| `Available` / `Aman` | `quantity > 500` |
| `Running Low` / `Menipis` | `0 < quantity <= 500` |
| `Out of Stock` / `Habis` | `quantity <= 0` |

## 5. Entitas: Pembayaran dan Tipe Order (Pendukung)
Tabel: `orders` (kolom `payment_method` & `order_type`)

| Field | Nilai Standar |
|---|---|
| `payment_method` | `Tunai`, `QRIS`, `Debit`, `Credit Card` |
| `order_type` | `Dine In`, `Takeaway` |

## 6. Entitas: Staff Shift (Absensi Shift)
Tabel: `staff_shifts` (kolom `status`)

| Status Backend | Keterangan |
|---|---|
| `Active` | Shift sedang berjalan (Staff sudah check-in). |
| `Completed` | Shift telah selesai (Staff sudah check-out). |
| `Cancelled` | Shift dibatalkan. |
