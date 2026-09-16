## Ringkasan

Lanjutan PR #34. Setelah PR #34 di-deploy, URL script Livewire sudah benar, tetapi halaman login **masih** merender `data-update-uri="/livewire/update"` tanpa prefix, sehingga tombol **Masuk** dan seluruh interaksi panel Filament tetap mati di live demo.

### Penyebab
Prefix `/edusaas` diketahui aplikasi dari header `X-Forwarded-Prefix`. Header itu baru dipercaya setelah middleware `TrustProxies` berjalan, sedangkan penentuan rute Livewire dilakukan lebih awal, saat provider `boot()`.

Akibatnya saat boot:
- `request()->getBaseUrl()` masih kosong, sehingga rute didaftarkan tanpa prefix;
- ketika URL dirender (setelah middleware), Laravel justru memotong `/edusaas` dari URL rute relatif.

Hasil akhirnya prefix hilang.

### Perbaikan
- Prefix dibaca langsung dari header `X-Forwarded-Prefix` saat boot, dengan `getBaseUrl()` sebagai cadangan. Nilai ini memprediksi *base path* yang nanti dipakai Laravel saat merender URL.
- Tiga skenario tetap benar:
  - sub-folder tanpa proxy (Apache/Nginx langsung);
  - sub-folder di belakang proxy yang memotong prefix (live demo);
  - instalasi di root domain (tidak tersentuh).

### Pengujian
`tests/Feature/SubdirectoryUrlTest.php` kini meniru urutan nyata di live: request dibuat dengan header, aplikasi di-boot **sebelum** proxy dipercaya, lalu `TrustProxies` disimulasikan, baru URL diperiksa.

### Pemeriksaan deploy diperketat
Skrip deploy VPS sebelumnya hanya **menampilkan** `data-update-uri`, sehingga deploy tetap dianggap sukses walau panel rusak. Sekarang skrip **gagal** (exit 1) bila:
- berkas CSS Filament tidak 200;
- `data-update-uri` tidak membawa `/edusaas`;
- `src` script Livewire tidak membawa `/edusaas`.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
