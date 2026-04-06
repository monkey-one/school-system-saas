# PR #11 — Fix URL Redirect, Bilingual Switcher & Seed Roles + Permissions

## Ringkasan

PR ini memperbaiki 3 masalah kritis yang ditemukan setelah deployment PR #10:

### 1. Fix Halaman Blank Setelah Login
- **Masalah:** Setelah login, user di-redirect ke `yourmoonkey.com/super-admin` (tanpa prefix `/school-system-demo`), sehingga halaman blank.
- **Penyebab:** Nginx memiliki location block `/livewire/` terpisah yang men-set `SCRIPT_NAME=/school-system-demo/index.php` tetapi `REQUEST_URI=/livewire/update`. Symfony tidak bisa mencocokkan keduanya → `baseUrl` kosong → `url()` menghasilkan URL tanpa prefix.
- **Solusi:** Tambah `URL::forceRootUrl(config('app.url'))` di `AppServiceProvider::boot()` supaya semua URL generation selalu menggunakan APP_URL yang benar.

### 2. Fix Bilingual Switcher Tidak Berfungsi
- **Masalah:** Klik tombol ID/EN tidak mengubah bahasa — halaman tetap di bahasa yang sama.
- **Penyebab:** Filament 3 menggunakan Livewire 3 dengan `wire:navigate` (SPA-like navigation). Ketika user klik link language switcher, Livewire mengintercept klik tersebut dan menangani redirect via XHR, bukan full page reload. Akibatnya locale berubah di session tapi halaman tidak di-render ulang.
- **Solusi:** Tambahkan `x-on:click.prevent` dengan `window.location.href` pada link language switcher untuk memaksa full page reload ketika mengganti bahasa.

### 3. Seed Data Roles & Permissions
- **Masalah:** Halaman Role Management dan Permission Management kosong — tidak ada data sama sekali.
- **Solusi:** Buat `RolesAndPermissionsSeeder` yang:
  - Membuat **4 roles**: `super_admin`, `school_admin`, `operator`, `teacher`
  - Membuat **150+ permissions** granular untuk semua resource (view, create, update, delete per module)
  - **Super Admin**: Mendapat SEMUA permissions
  - **School Admin**: Mendapat semua permissions level sekolah (academic, students, staff, attendance, grading, finance, library, inventory, dll)
  - **Operator**: Mendapat permissions sekolah tanpa delete dan tanpa finance
  - **Teacher**: Mendapat permissions panel guru saja (jadwal, absensi, nilai)
  - Auto-assign role ke semua user yang sudah ada berdasarkan `UserType` mereka

## File yang Diubah

| File | Perubahan |
|------|-----------|
| `app/Providers/AppServiceProvider.php` | Tambah `URL::forceRootUrl()` untuk fix redirect |
| `resources/views/filament/language-switcher.blade.php` | Tambah `x-on:click.prevent` bypass wire:navigate |
| `database/seeders/RolesAndPermissionsSeeder.php` | **NEW** — Seeder roles & permissions |

## Deployment Notes

Setelah deploy, jalankan seeder:
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder --force
php artisan optimize:clear
```
