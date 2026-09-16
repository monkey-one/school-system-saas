## Ringkasan

Dokumentasi internal (`CLAUDE.md`) diperbarui agar kejadian panel Filament mati di live demo tidak terulang.

### Tambahan aturan kerja
- **Verifikasi hasil render, bukan sekadar HTTP 200.** Halaman dapat membalas 200 walau CSS-nya 404 dan tombolnya tidak berfungsi. Setelah deploy wajib memastikan CSS Filament 200 serta `data-update-uri` dan `src` script Livewire membawa prefix `/edusaas`. Skrip deploy di VPS kini keluar dengan status 1 bila salah satu gagal.

### Tambahan catatan arsitektur
- URL Livewire dibuat berprefix oleh `AppServiceProvider::configureSubdirectoryAssets()`, yang membaca header `X-Forwarded-Prefix` saat boot karena `TrustProxies` baru berjalan setelah provider boot.
- Aset Filament (`public/css`, `public/js`) tidak disimpan di git, sehingga perlu `php artisan filament:assets` setiap deploy. `composer install` juga sudah memicunya lewat `filament:upgrade`.

Tidak ada perubahan kode aplikasi pada PR ini.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
