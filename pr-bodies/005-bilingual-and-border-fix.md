## Deskripsi

PR ini menambahkan dukungan bilingual (Indonesia/Inggris) ke seluruh aplikasi dan memperbaiki border connector yang tidak sejajar di section "3 Langkah" pada landing page.

## Perubahan

### 🔧 Perbaikan Border
- Memperbaiki posisi garis connector antar langkah di section "Mulai dalam 3 Langkah" yang tidak sejajar dengan lingkaran nomor (`top-24` → `top-8`, `left-1/4 right-1/4` → `left-[20%] right-[20%]`)

### 🌐 Sistem Bilingual (Indonesia / Inggris)

#### Infrastruktur
- Menambahkan `SetLocale` middleware (session-based) di web middleware stack
- Menambahkan route `/locale/{locale}` untuk switching bahasa
- Mengubah default locale ke `id` (Indonesia)
- Membuat file translasi `lang/id.json` dan `lang/en.json` dengan 250+ key

#### Landing Page
- Seluruh teks dikonversi menggunakan `__()` helper
- Language switcher ditambahkan di navbar (desktop: dropdown dengan bendera 🇮🇩/🇬🇧, mobile: tombol ID/EN)

#### Student Portal (2 views)
- Layout: nav labels bilingual, language switcher di header
- Dashboard: semua card, section, dan empty state bilingual

#### Parent Portal (6 views)
- Layout: nav labels, child selector, language switcher di header
- Dashboard: attendance summary, grades, SPP status, messages
- Attendance: summary cards, tabel kehadiran, filter bulan
- Grades: tabel nilai, semester selector, rata-rata
- SPP: ringkasan tagihan, daftar tagihan, status pembayaran
- Messages: form compose, riwayat pesan

#### PPDB (3 views)
- Index: header, wave cards (kuota/biaya), empty state
- Register: 4 step form labels, JS stepLabels via `@json(__())`, validasi, halaman sukses
- Status: form pencarian, JS statusLabels, error messages

#### Filament Admin Panels
- Login page: subtitle panel bilingual
- SchoolAdminPanelProvider: brandName + 10 navigation group bilingual (Akademik, Kesiswaan, Kepegawaian, Presensi, Penilaian, Keuangan, Komunikasi, Perpustakaan, Inventaris, Pengaturan)
- SuperAdminPanelProvider: brandName bilingual
- TeacherPanelProvider: brandName bilingual

## File yang Diubah (22 file)

| Kategori | File |
|----------|------|
| Middleware | `app/Http/Middleware/SetLocale.php` (baru) |
| Config | `config/app.php`, `bootstrap/app.php` |
| Routes | `routes/web.php` |
| Lang | `lang/id.json`, `lang/en.json` |
| Landing | `resources/views/landing/index.blade.php` |
| Student Portal | `layout.blade.php`, `dashboard.blade.php` |
| Parent Portal | `layout.blade.php`, `dashboard.blade.php`, `attendance.blade.php`, `grades.blade.php`, `spp.blade.php`, `messages.blade.php` |
| PPDB | `index.blade.php`, `register.blade.php`, `status.blade.php` |
| Filament | `login.blade.php`, `SchoolAdminPanelProvider.php`, `SuperAdminPanelProvider.php`, `TeacherPanelProvider.php` |

## Catatan Deploy
- Setelah deploy, tambahkan kembali `yourmoonkey.com` ke array `baseDomains` di `ResolveTenant` middleware (perubahan lokal VPS)
- Jalankan `php artisan optimize:clear && php artisan filament:assets`
