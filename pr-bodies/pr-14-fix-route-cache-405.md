# PR #14: Fix 405 Method Not Allowed pada Landing Page

## Ringkasan
Memperbaiki error **405 Method Not Allowed** yang terjadi pada halaman landing (`/school-system-demo/`) setelah menjalankan `php artisan route:cache`.

## Penyebab
Laravel tidak bisa melakukan serialisasi/cache terhadap route yang menggunakan **closure** (anonymous function). Terdapat 2 route closure di `routes/web.php`:
1. `locale.switch` — untuk mengganti bahasa
2. `logout` — untuk logout dari portal siswa/orang tua

Ketika `route:cache` dijalankan, proses serialisasi gagal dan menghasilkan cache route yang rusak, sehingga semua route mengembalikan error 405.

## Solusi
- Memindahkan logika dari closure ke **MiscController** (`switchLocale()` dan `logout()`)
- Route sekarang menggunakan format `[Controller::class, 'method']` yang bisa di-cache

## File yang Diubah
- `app/Http/Controllers/MiscController.php` (BARU) — Controller untuk locale switch dan logout
- `routes/web.php` (DIPERBARUI) — Ganti 2 closure route menjadi controller route, hapus import `Auth` facade yang tidak diperlukan lagi

## Dampak
- Landing page dan semua route berfungsi normal kembali
- `route:cache` sekarang bisa dijalankan tanpa error
- Tidak ada perubahan fungsional — perilaku locale switch dan logout tetap sama
