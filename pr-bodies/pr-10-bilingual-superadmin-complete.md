# Fix: Bilingual Translations + Complete SuperAdmin Panel

## Ringkasan
PR ini memperbaiki fitur bilingual yang sebelumnya tidak berfungsi penuh dan melengkapi panel SuperAdmin dengan fitur-fitur penting yang belum ada.

## Perubahan Utama

### 1. Perbaikan Bilingual
- **Publish semua translation Filament vendor** (panels, actions, tables, forms, notifications, infolists) → Sekarang semua komponen UI bawaan Filament (tombol, label, pesan) diterjemahkan ke Bahasa Indonesia
- **40+ key translasi baru** ditambahkan ke `en.json` dan `id.json` untuk mendukung fitur-fitur baru
- **Widget dashboard SuperAdmin** yang sebelumnya hardcoded Bahasa Indonesia sekarang menggunakan `__()` sehingga mendukung pergantian bahasa

### 2. Manajemen Role & Permission
- **Tambah Spatie `HasRoles` trait** ke model `User` → User sekarang bisa memiliki role dan permission
- **RoleResource baru** → CRUD role dengan assign permission langsung dari form
- **PermissionResource baru** → CRUD permission dengan assign role langsung dari form
- **UserResource diperbarui** → Tambah kolom dan field roles di form dan tabel user

### 3. Activity Log
- **Publish migration** `activity_log` table (spatie/laravel-activitylog)
- **ActivityLogResource baru** → Browser log aktivitas dengan filter, detail view, dan tampilan old/new values

### 4. NavigasiPanel SuperAdmin
- **Grup navigasi baru "System"** → Menampung Activity Log
- Grup navigasi "User Management" sekarang menampung: All Users, Roles, Permissions
- Grup navigasi "Tenant Management" tetap: Schools, Plans, Subscriptions

## Dampak
- Tidak ada breaking changes
- Perlu `php artisan migrate` di VPS untuk tabel `activity_log`
- Cache perlu di-clear setelah deploy (`php artisan optimize:clear`)
- Spatie permission tables sudah ada dari migrasi sebelumnya, jadi Role/Permission bisa langsung digunakan

## File yang Diubah/Ditambah
- `app/Models/User.php` → Tambah `HasRoles` trait
- `app/Filament/SuperAdmin/Resources/RoleResource.php` + pages (baru)
- `app/Filament/SuperAdmin/Resources/PermissionResource.php` + pages (baru)
- `app/Filament/SuperAdmin/Resources/ActivityLogResource.php` + pages (baru)
- `app/Filament/SuperAdmin/Resources/UserResource.php` → Tambah roles field
- `app/Providers/Filament/SuperAdminPanelProvider.php` → Tambah grup System
- `app/Filament/SuperAdmin/Widgets/*.php` → Bilingual dengan `__()` 
- `lang/en.json`, `lang/id.json` → 40+ key baru
- `lang/vendor/filament-*` → Semua translation vendor Filament
- `database/migrations/2026_04_06_*_activity_log*.php` → Migration activity log
