# PR #15 — Fix 405 Method Not Allowed pada Landing Page dengan Route Cache

## Ringkasan

Memperbaiki error **405 Method Not Allowed** pada landing page (`/`) yang terjadi ketika `route:cache` diaktifkan pada deployment subdirektori `/school-system-demo`.

## Akar Masalah

Ketika aplikasi di-deploy di bawah subdirektori (`/school-system-demo`), method `CompiledRouteCollection::requestWithoutTrailingSlash()` di Laravel menghapus trailing slash dari `REQUEST_URI`:

- **Sebelum trim:** `REQUEST_URI = /school-system-demo/`
- **Setelah trim:** `REQUEST_URI = /school-system-demo` (tanpa trailing slash)

Tanpa trailing slash, Symfony tidak bisa membedakan base URL (`/school-system-demo`) dari path yang diminta. Hasilnya:
- `baseUrl` berubah dari `/school-system-demo` → kosong (`""`)
- `pathInfo` berubah dari `/` → `/school-system-demo`

Route matcher kemudian mencari route `/school-system-demo` (bukan `/`), tidak menemukannya, dan melempar error **405 Method Not Allowed**.

## Solusi

Menambahkan prefix stripping di `public/index.php` **sebelum** Laravel memproses request:
- `REQUEST_URI`: `/school-system-demo/register` → `/register`
- `SCRIPT_NAME`: `/school-system-demo/index.php` → `/index.php`
- `PHP_SELF`: `/school-system-demo/index.php` → `/index.php`

URL generation tetap benar karena `URL::forceRootUrl()` di `AppServiceProvider` sudah mengatur root URL ke `APP_URL` (`https://yourmoonkey.com/school-system-demo`).

## File yang Diubah

| File | Perubahan |
|------|-----------|
| `public/index.php` | Menambahkan stripping prefix `/school-system-demo` dari `$_SERVER` vars |

## Testing

Semua route sudah diverifikasi return **200 OK** dengan semua cache aktif (`route:cache`, `config:cache`, `view:cache`, `event:cache`):
- ✅ Landing page (`/`)
- ✅ Register (`/register`)
- ✅ Super admin login (`/super-admin/login`)
- ✅ School admin login (`/school/login`)
- ✅ Locale switch (`/locale/en`) → 302 redirect

## Dampak

- **Positif:** Landing page sekarang berfungsi dengan `route:cache` aktif, meningkatkan performa routing
- **Risiko rendah:** Perubahan hanya di entry point, tidak mengubah logic bisnis apapun
