# PR #20 - Fix: Login Terpadu Bisa Digunakan Semua Role

## Ringkasan

Memperbaiki bug di halaman login terpadu (`/school/login`) yang sebelumnya hanya bisa digunakan oleh role School Admin dan Operator. User dengan role Super Admin dan Teacher mendapat error "These credentials do not match our records" meskipun email dan password benar.

## Penyebab Bug

Method `parent::authenticate()` dari Filament memanggil `canAccessPanel()` terhadap panel saat ini (`school-admin`). Karena Super Admin dan Teacher bukan role yang diizinkan di panel school-admin, Filament logout user dan menampilkan error "credentials do not match".

## Solusi

Override method `authenticate()` secara lengkap tanpa memanggil `parent::authenticate()`. Proses autentikasi dilakukan langsung:
1. Rate limiting (tetap dipertahankan)
2. `Filament::auth()->attempt()` — cek email dan password
3. Cek `is_active` — pastikan akun aktif (BUKAN `canAccessPanel()`)
4. Regenerate session
5. Redirect ke panel yang sesuai berdasarkan `UserType`

## File yang Diubah

- `app/Filament/Pages/Auth/Login.php` — Override authenticate() lengkap

## Dampak
- Super Admin, Teacher, School Admin, dan Operator sekarang bisa login dari halaman yang sama
- Tidak ada perubahan database
- Tidak ada perubahan UI
