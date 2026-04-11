# PR #24: Sistem Manajemen Mata Uang Terpusat

## Ringkasan

Menambahkan sistem manajemen mata uang yang terpusat dan terintegrasi ke seluruh aplikasi. Sebelumnya mata uang "Rp" (IDR) di-hardcode di 48+ lokasi di seluruh codebase. Sekarang mata uang dapat dikonfigurasi melalui:

1. **Super Admin** → System Settings → Default Currency (platform-wide default)
2. **School Admin** → School Profile → Currency (per-sekolah override)

## Perubahan Utama

### File Baru
- **`app/Helpers/CurrencyHelper.php`** — Helper terpusat dengan method:
  - `format($amount)` — Format angka dengan simbol mata uang (misal: "Rp 500.000" atau "$ 500.00")
  - `symbol()` — Ambil simbol mata uang aktif
  - `code()` — Ambil kode ISO mata uang (IDR, USD, dll)
  - `options()` — Daftar mata uang untuk dropdown
  - Prioritas: Tenant currency → System default → fallback IDR
- **`database/migrations/..._add_currency_to_tenants_table.php`** — Kolom `currency` di tabel tenants

### Mata Uang yang Didukung (12)
IDR (Rp), USD ($), EUR (€), MYR (RM), SGD (S$), GBP (£), JPY (¥), AUD (A$), THB (฿), PHP (₱), INR (₹), BRL (R$)

### File yang Dimodifikasi (26 file)

**Panel Super Admin:**
- `SystemSettings.php` — Tambah dropdown Default Currency
- `PlanResource.php` — Dynamic prefix & money() 
- `SuperAdminStatsOverview.php` — MRR widget pakai CurrencyHelper

**Panel School Admin:**
- `SchoolProfile.php` — Tambah dropdown Currency di profil sekolah
- `SppBillResource.php` — Form prefix & table money() dinamis
- `PaymentResource.php` — Form prefix & table money() dinamis
- `SppTypeResource.php` — Form prefix & table money() dinamis
- `SppDiscountResource.php` — Format value dinamis
- `BookLoanResource.php` — Denda prefix dinamis
- `AssetResource.php` — Nilai aset prefix dinamis
- `StatsOverview.php` — Widget pendapatan SPP bulanan
- `LateSppWidget.php` — Widget tagihan terlambat

**Blade Views (8 file):**
- `landing/index.blade.php` — 3 pricing card
- `registration/payment.blade.php` — Halaman pembayaran
- `registration/index.blade.php` — JavaScript formatCurrency()
- `parent-portal/dashboard.blade.php` — Status SPP
- `parent-portal/spp.blade.php` — Tabel tagihan + summary
- `student-portal/spp.blade.php` — Tabel tagihan + summary
- `ppdb/index.blade.php` — Biaya pendaftaran
- `pdf/receipt.blade.php` — Kwitansi PDF

**Services & Models:**
- `XenditService.php` — Dynamic currency code
- `Tenant.php` — Tambah `currency` ke fillable

**Translations:**
- `lang/id.json` & `lang/en.json` — Label mata uang bilingual

## Dampak
- **Backward compatible** — Default tetap IDR, tidak ada perubahan behavior untuk instalasi existing
- **Per-tenant** — Setiap sekolah bisa memilih mata uang sendiri
- **Platform-wide default** — Super admin bisa set default untuk semua sekolah baru
- Perlu `php artisan migrate` saat deploy untuk menambah kolom currency
