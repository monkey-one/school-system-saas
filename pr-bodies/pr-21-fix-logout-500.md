# PR #21 - Fix: Logout 500 Error pada Panel Super Admin & Teacher

## Ringkasan

Memperbaiki error 500 saat logout dari panel Super Admin (`/super-admin/logout`) dan Teacher (`/teacher/logout`).

## Penyebab Bug

Di `routes/web.php`, ada 2 redirect route:
```php
Route::get('/super-admin/login', fn () => redirect('/school/login'));
Route::get('/teacher/login', fn () => redirect('/school/login'));
```

Route ini **menimpa** named route yang didaftarkan oleh Filament untuk setiap panel (`filament.super-admin.auth.login` dan `filament.teacher.auth.login`). Saat proses logout, Filament mencoba redirect ke named route tersebut, tapi karena sudah ditimpa, route tidak ditemukan → **500 Server Error**.

## Solusi

Hapus kedua redirect route tersebut. Tidak diperlukan karena semua panel sudah menggunakan `Login::class` yang sama dan view yang sama. Setiap panel memiliki halaman login sendiri di URL masing-masing (`/super-admin/login`, `/teacher/login`, `/school/login`), tapi semuanya menampilkan form login yang sama dan redirect ke panel yang benar setelah autentikasi.

Hanya redirect `/login` → `/school/login` yang dipertahankan (untuk Laravel auth middleware default).

## File yang Diubah
- `routes/web.php` — Hapus 2 redirect route yang konflik

## Dampak
- Logout dari semua panel sekarang berfungsi normal
- Login tetap bisa dari URL panel mana saja (semua menampilkan form yang sama)
- Tidak ada perubahan database
