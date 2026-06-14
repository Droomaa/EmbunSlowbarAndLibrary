# API Contract Final - Embun Cafe

| URL | Method | Auth | Role | Controller Action | Keterangan |
|---|---|---|---|---|---|
| **Public Routes** |
| `/api/login` | POST | None | All | AuthController@login | Autentikasi user |
| `/api/register` | POST | None | All | AuthController@register | Buat akun baru |
| `/api/menus` | GET | None | All | MenuController@index | Ambil katalog menu |
| `/api/addons` | GET | None | All | - | Ambil semua add-ons |
| `/api/orders` | POST | Throttle | All | OrderController@store | Customer order baru |
| `/api/reservations` | POST | Throttle | All | ReservationController@store | Customer reservasi |
| **Global Auth** |
| `/api/me` | GET | Sanctum | All | (Closure) | Info user saat ini |
| `/api/logout` | POST | Sanctum | All | AuthController@logout | Hapus token |
| `/api/inventory` | GET/POST/PATCH/DELETE | Sanctum | Staff,Admin,Owner | InventoryController | CRUD Stok gudang |
| **Owner Routes** |
| `/api/owner/dashboard/data` | GET | Sanctum | Owner | OwnerDashboardController@getOverview | Stats dashboard owner |
| `/api/owner/reports/data` | GET | Sanctum | Owner | OwnerDashboardController@getSalesReports | Laporan penjualan |
| `/api/owner/stock/data` | GET | Sanctum | Owner | OwnerDashboardController@getStockReports | Laporan stok owner |
| `/api/owner/accounts` | GET/POST/DELETE | Sanctum | Owner | AccountManagementController | CRUD Akun pegawai |
| `/api/owner/menus/data` | GET | Sanctum | Owner | OwnerDashboardController@getMenus | Daftar menu (Admin) |
| `/api/owner/transactions/data` | GET | Sanctum | Owner | OwnerDashboardController@getTransactions | Riwayat transaksi |
| `/api/owner/transactions/{id}/status` | PUT | Sanctum | Owner | OwnerDashboardController@updateTransactionStatus | Update & potong stok |
| **Admin Routes** |
| `/api/admin/dashboard-stats` | GET | Sanctum | Admin | AdminDashboardController@index | Stats dashboard admin |
| `/api/admin/stock-report` | GET | Sanctum | Admin | AdminStokController@index | Laporan stok |
| `/api/admin/sales-report` | GET | Sanctum | Admin | AdminPenjualanController@index | Laporan penjualan |
| **Karyawan Routes** |
| `/api/karyawan/dashboard/data` | GET | Sanctum | Staff | KaryawanDashboardController@getDashboardData | Stats staff |
| `/api/karyawan/pos/menus` | GET | Sanctum | Staff,Admin | KaryawanKasirController@getMenus | Kasir menu list |
| `/api/karyawan/pos/checkout` | POST | Sanctum | Staff,Admin | KaryawanKasirController@checkout | Submit POS order |
| `/api/karyawan/orders/offline` | GET | Sanctum | Staff,Admin | KaryawanKasirController@getOfflineOrders | Riwayat kasir offline |
| `/api/karyawan/orders/online` | GET | Sanctum | Staff | KaryawanOnlineController@getOnlineOrders | List pesanan online |
| `/api/karyawan/orders/{id}/status` | POST | Sanctum | Staff | KaryawanOnlineController@updateOrderStatus | Update online order |
| `/api/karyawan/reservations/data` | GET | Sanctum | Staff | KaryawanReservasiController@index | List reservasi masuk |
| `/api/karyawan/reservations/{id}/status` | POST | Sanctum | Staff | KaryawanReservasiController@updateStatus | Accept/Reject reservasi |
| `/api/karyawan/shift/current` | GET | Sanctum | Staff | StaffShiftController@current | Ambil shift aktif |
| `/api/karyawan/shift/check-in` | POST | Sanctum | Staff | StaffShiftController@checkIn | Mulai shift baru |
| `/api/karyawan/shift/check-out` | POST | Sanctum | Staff | StaffShiftController@checkOut | Akhiri shift |
| `/api/karyawan/shift/history` | GET | Sanctum | Staff | StaffShiftController@history | Riwayat shift staff |
