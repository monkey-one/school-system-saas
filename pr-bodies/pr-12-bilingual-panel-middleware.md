# PR #12 — Fix Bilingual: Tambah SetLocale Middleware ke Semua Panel Filament

## Ringkasan

PR ini memperbaiki masalah bilingual (ID/EN) yang tidak berfungsi pada semua panel Filament.

### Root Cause

Panel Filament (SuperAdmin, SchoolAdmin, Teacher) menggunakan **middleware stack sendiri**, bukan middleware group `web`. Middleware `SetLocale` hanya ditambahkan ke group `web` di `bootstrap/app.php`, sehingga **tidak pernah berjalan** pada route-route panel Filament.

Akibatnya, `app()->setLocale()` tidak pernah dipanggil untuk halaman panel → semua halaman panel selalu menggunakan locale default (`id`) tanpa peduli apa yang disimpan di session.

### Bukti Diagnosis

1. ✅ Route `/locale/{locale}` berhasil menyimpan locale ke session
2. ✅ Session di database memiliki `locale: "en"` setelah switch
3. ✅ Debug route menunjukkan `app_locale: "en"` dengan benar
4. ❌ Halaman Filament tetap menampilkan bahasa Indonesia
5. ❌ Bahkan dengan middleware SetLocale yang di-hardcode ke `'en'`, halaman Filament tetap Indonesia
6. ✅ `php artisan route:list -v` menunjukkan `SetLocale` TIDAK ada di middleware stack panel Filament

### Solusi

Tambahkan `SetLocale::class` ke array `->middleware()` di **ketiga panel provider**:
- `SuperAdminPanelProvider.php`
- `SchoolAdminPanelProvider.php`  
- `TeacherPanelProvider.php`

### Perubahan Tambahan

Revert approach Alpine.js `x-on:click.prevent` pada language-switcher.blade.php yang menyebabkan CSP (Content Security Policy) error. Plain `<a>` tags sudah cukup karena dengan middleware yang benar, locale berubah di session dan diterapkan pada request berikutnya.

## File yang Diubah

| File | Perubahan |
|------|-----------|
| `app/Providers/Filament/SuperAdminPanelProvider.php` | Tambah `SetLocale::class` ke middleware |
| `app/Providers/Filament/SchoolAdminPanelProvider.php` | Tambah `SetLocale::class` ke middleware |
| `app/Providers/Filament/TeacherPanelProvider.php` | Tambah `SetLocale::class` ke middleware |
| `resources/views/filament/language-switcher.blade.php` | Revert ke plain `<a>` tags (hapus x-on:click CSP issue) |
