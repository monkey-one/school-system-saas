# Fix: Trust Proxies, Personal Access Tokens & Opacity Fallback

## Ringkasan

PR ini memperbaiki beberapa masalah deployment yang menyebabkan halaman blank setelah login dan konfigurasi proxy yang tidak tepat di belakang Cloudflare CDN.

## Perubahan

### 1. Konfigurasi TrustProxies untuk Cloudflare (`bootstrap/app.php`)
- Menambahkan `$middleware->trustProxies()` dengan trust `*` (semua proxy)
- Mengkonfigurasi header forwarded: `X-Forwarded-For`, `X-Forwarded-Host`, `X-Forwarded-Port`, `X-Forwarded-Proto`
- **Penting**: Tanpa konfigurasi ini, Laravel tidak dapat mendeteksi HTTPS dan IP asli client dengan benar saat di belakang Cloudflare

### 2. Migrasi `personal_access_tokens` (`database/migrations/...`)
- Membuat tabel `personal_access_tokens` yang diperlukan oleh Laravel Sanctum
- Tabel ini belum ada di database dan menyebabkan error saat fitur API token digunakan
- Struktur: `id`, `tokenable` (morph), `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `timestamps`

### 3. CSS Opacity Fallback (`app/Providers/AppServiceProvider.php`)
- Menambahkan CSS animation fallback melalui Filament render hook `STYLES_AFTER`
- Jika Alpine.js terlambat inisialisasi (jaringan lambat, Cloudflare challenge, atau loading script), konten utama tetap terlihat setelah 2 detik
- Mengatasi masalah "blank page" dimana `.fi-main-ctn.opacity-0` membuat konten tidak terlihat karena Alpine.js belum mengubah opacity ke 1

## Dampak
- Aplikasi akan bekerja dengan benar di belakang Cloudflare CDN
- Halaman dashboard tidak akan blank meskipun JavaScript loading terlambat
- API token (Sanctum) dapat digunakan tanpa error missing table
- Tidak ada breaking changes

## Deployment Notes
- Jalankan `php artisan migrate` untuk membuat tabel `personal_access_tokens`
- Hapus CSP header custom dari Nginx config (ditambahkan saat debugging sebelumnya)
- Clear semua cache: `php artisan optimize:clear`
- Hapus test routes yang ditambahkan langsung di VPS
