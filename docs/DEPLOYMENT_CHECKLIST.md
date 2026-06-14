# Pre-Deployment Checklist - Embun Cafe

Ceklist ini wajib dijalankan sebelum memindahkan kode ke staging atau production public.

## 1. Database & Migrations
- [ ] Pastikan tidak memakai `php artisan migrate:fresh --seed` di production! Hanya gunakan `php artisan migrate`.
- [ ] Lakukan backup database production (`mysqldump`) sebelum deploy versi baru.
- [ ] Verifikasi semua kolom migration baru (`menus.category`, `orders.payment_method`) telah terbentuk sempurna di DB prod.

## 2. Environment Configurations (`.env`)
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `CORS_ALLOWED_ORIGINS` diubah dari `*` menjadi spesifik domain frontend (misal: `https://app.embuncafe.com`).
- [ ] Set `DB_PASSWORD` dengan kredensial yang kuat.

## 3. Storage & Cache
- [ ] Jalankan `php artisan storage:link` di server production.
- [ ] Jalankan `php artisan optimize` (config, route, view cache).

## 4. Frontend (Vite)
- [ ] Setup `VITE_API_BASE_URL` di server hosting frontend ke endpoint API backend (misal: `https://api.embuncafe.com/api`).
- [ ] Jalankan `npm run build` dan deploy folder `dist/`.

## 5. Keamanan & Performa
- [ ] Pastikan semua route public non-auth (`/api/orders`, `/api/reservations`) memiliki *rate limiting* / `throttle`.
- [ ] Nonaktifkan *Directory Listing* di web server (Nginx/Apache).
- [ ] Paksa HTTP ke HTTPS (SSL Certificate).
