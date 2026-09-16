## Ringkasan

Perbaikan kritis untuk instalasi yang dijalankan di **sub-folder** (contoh: `https://numintek.com/edusaas/`). Di live demo, seluruh panel Filament tidak dapat dipakai: tampilan tanpa CSS dan tombol **Masuk** tidak berfungsi.

### Masalah 1 — Livewire memakai URL tanpa prefix
- Halaman login merender `data-update-uri="/livewire/update"` dan `<script src="/livewire/livewire.min.js">`, tanpa `/edusaas`.
- Akibatnya browser mengirim permintaan ke `numintek.com/livewire/update` (di luar aplikasi), sehingga form login, semua tabel, dan semua modal Filament mati.
- Penyebab: Laravel menghapus *base path* permintaan dari URL rute relatif. Prefix diketahui dari header `X-Forwarded-Prefix` yang mulai dipercaya sejak PR #28 (agar tautan bertanda tangan valid).

**Perbaikan** (`AppServiceProvider::configureSubdirectoryAssets`)
- Jika `APP_URL` memiliki path (mis. `/edusaas`):
  - `livewire.asset_url` diarahkan ke `APP_URL/livewire/livewire.min.js`;
  - rute update Livewire didaftarkan ulang dengan prefix, sehingga URL yang dirender ikut membawa `/edusaas`.
- Rute bawaan Livewire tetap ada dan tetap cocok dengan permintaan masuk setelah web server memotong prefix.
- Instalasi di root domain tidak terpengaruh sama sekali.

### Masalah 2 — Aset Filament tidak pernah dipublikasikan
- `public/css` dan `public/js` tidak ada di server, sehingga semua berkas CSS/JS Filament menghasilkan 404 dan panel tampil tanpa gaya.
- Folder tersebut memang tidak disimpan di git, dan tidak ada proses yang menerbitkannya saat deploy.

**Perbaikan**
- `composer.json`: `@php artisan filament:upgrade` ditambahkan ke `post-autoload-dump`, sehingga aset otomatis terbit setiap `composer install` — termasuk untuk pembeli.
- Skrip deploy VPS kini menjalankan `php artisan filament:assets` sebagai root (folder `public/` milik root), lalu memeriksa hasilnya.

### Pemeriksaan deploy diperketat
Smoke test lama hanya memeriksa kode HTTP 200, sehingga halaman rusak tetap lolos. Skrip deploy sekarang juga memeriksa:
- berkas CSS Filament benar-benar dapat diunduh (200);
- `data-update-uri` yang dirender membawa prefix `/edusaas`.

### Pengujian
- `tests/Feature/SubdirectoryUrlTest.php`:
  - `APP_URL` dengan sub-folder menghasilkan URL script dan update Livewire yang berprefix;
  - instalasi di root domain tetap memakai `/livewire/update`.
- Seluruh test suite dan crawler halaman dijalankan di VPS.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
