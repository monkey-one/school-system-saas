# EduSaaS — Multi-Tenant School Management System

Produk jualan Codester milik **PT Danum Inovasi Teknologi (numintek.com)**.
Laravel 11 + Filament 3 + Livewire 3, MySQL 8, PHP 8.2+ (VPS: 8.4).

## Aturan kerja (wajib)

- **Jangan pernah menjalankan aplikasi, composer, npm, atau test di mesin lokal** —
  storage lokal penuh. Semua eksekusi (artisan, tests, build, seeding) dilakukan di VPS.
- Setiap perubahan: buat branch baru → commit → push → PR (body disimpan di
  `pr-bodies/`) → merge ke `main` → deploy ke VPS. Jangan hapus branch.
- Ringkasan PR ditulis dalam Bahasa Indonesia.
- Branding: **numintek / Danum Inovasi Teknologi**. Tidak boleh ada kata "moonkey"
  di kode, dokumen, maupun paket rilis.
- `git config core.fileMode false` — repo lokal punya permission 755 yang bukan perubahan nyata.

## Deploy / live demo

| Item | Nilai |
|---|---|
| VPS | `ssh root@194.233.83.157` |
| Live demo | `https://numintek.com/edusaas/` |
| Direktori | `/var/www/edusaas` (remote `github-edusaas:monkey-one/school-system-saas.git`, deploy key read-only) |
| Database | MySQL `edusaas` |
| Nginx | blok `/edusaas/` di `/etc/nginx/sites-enabled/numintek` (pola alias + php8.4-fpm, `SCRIPT_NAME` diberi prefix) |
| Worker | supervisor `edusaas-live-worker`, cron `schedule:run` |
| Demo lama | `/var/www/school-system-demo` (yourmoonkey.com, DNS masih server lama) — jangan diubah |

Setelah menyentuh nginx, uji tetangga produksi:
`for p in / /oper-app/ /btn-sfms/ /danum-books/ /btn-monev-demo/ /edusaas/; do curl -sL -o /dev/null -w "$p %{http_code}\n" https://numintek.com$p; done`

`DEMO_MODE=true` di `.env` VPS mengaktifkan banner/CTA numintek, kredensial demo di
halaman login, dan reset data terjadwal. Paket Codester dibangun dengan `DEMO_MODE=false`.

## Arsitektur

- **Tenancy**: single database, kolom `tenant_id` + trait `App\Traits\BelongsToTenant`
  (global scope + auto-fill). `App\Http\Middleware\ResolveTenant` menentukan tenant
  dari subdomain → `?tenant=` → `APP_DEFAULT_TENANT`. `Tenant::current()` adalah
  static holder (bukan container spatie). Super admin (`tenant_id = null`) di-bypass.
- **Panel Filament**: `school-admin` (`/edusaas-admin`, satu-satunya halaman login untuk
  semua role), `super-admin` (`/super-admin`), `teacher` (`/teacher`).
  `App\Filament\Pages\Auth\Login` mengarahkan user ke panel/portal sesuai `UserType`.
- **Portal Blade** (Tailwind CDN + Alpine): `student-portal`, `parent-portal`.
  Orang tua dihubungkan ke anak melalui `student_parents.email` = `users.email`.
- **Publik**: landing `/`, registrasi sekolah `/register`, PPDB `/ppdb`, profil sekolah
  `/profile`, alumni `/alumni`, scan absensi QR `/attendance/scan`.
- **API**: `routes/api.php` v1, Sanctum.
- **Integrasi**: Midtrans/Xendit (`app/Services`), WhatsApp Fonnte (`WhatsAppService`,
  job `SendWhatsAppNotification`), PDF dompdf, Excel maatwebsite + Filament Import/Export.
- **Mata uang**: selalu pakai `App\Helpers\CurrencyHelper` — jangan hardcode "Rp".
- **i18n**: string UI dibungkus `__()`; kunci di `lang/id.json` dan `lang/en.json`
  (keduanya harus diperbarui bersamaan). Locale via `SetLocale` + `/locale/{locale}`.
- Deploy di sub-path: `AppServiceProvider` memaksa `URL::forceRootUrl(APP_URL)`;
  jangan pakai path absolut `/...` di Blade — pakai `url()`/`route()`/`asset()`.

## Konvensi kode

- Resource Filament per panel di `app/Filament/{SchoolAdmin,SuperAdmin,Teacher}`.
- Setiap model tenant memakai `BelongsToTenant`; query lintas tenant hanya di super admin.
- Enum di `app/Enums` dengan method `label()`.
- Komentar gaya yang ada: blok komentar singkat di atas class/method yang menjelaskan *kenapa*.

## Perintah (jalankan di VPS, dari `/var/www/edusaas`)

```bash
php artisan migrate --force
php artisan db:seed --class=DemoSeeder --force
php artisan test
php artisan optimize:clear && php artisan optimize && php artisan filament:optimize
```

## Akun demo (password: `password`)

Lihat `DemoSeeder` — super admin `superadmin@edusaas.id`, admin `admin@smpn1demo.id`,
guru, siswa, dan orang tua. Daftar lengkap ada di halaman login saat `DEMO_MODE=true`.
