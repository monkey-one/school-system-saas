# EduSaaS — Sistem Manajemen Sekolah SaaS

> Multi-tenant SaaS School Management System dibangun dengan Laravel 11 + Filament PHP 3.x

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-blue)](https://php.net)
[![Filament](https://img.shields.io/badge/Filament-3.x-orange)](https://filamentphp.com)
[![License](https://img.shields.io/badge/License-Regular-green)](LICENSE)

---

## 🎯 Apa itu EduSaaS?

EduSaaS adalah sistem manajemen sekolah SaaS multi-tenant yang lengkap dan siap produksi. Setiap sekolah mendapatkan subdomain sendiri dan data terisolasi.

**Cocok untuk:**
- Entrepreneur yang ingin membangun bisnis SaaS sekolah
- Sekolah yang membutuhkan sistem manajemen lengkap
- Developer yang ingin starter SaaS Laravel premium

---

## ✨ Fitur Utama

- 🏫 **Multi-Tenant** — Setiap sekolah = subdomain + data terisolasi
- 📋 **PPDB Online** — Pendaftaran siswa baru online
- 📊 **Absensi QR Code** — Siswa scan QR via browser HP
- 💰 **SPP & Pembayaran Online** — Midtrans (QRIS, GoPay, VA) + Xendit
- 📱 **Notifikasi WhatsApp** — Alert otomatis ke orang tua via Fonnte API
- 📝 **Rapor Digital** — Generate & kirim rapor via WhatsApp (Kurikulum Merdeka)
- 🌐 **Portal Siswa & Orang Tua** — Portal self-service web
- 🔌 **REST API** — Mobile app ready dengan Sanctum auth
- 📚 **Perpustakaan & Inventaris** — Peminjaman buku + manajemen aset
- 📈 **Dashboard SaaS** — Kelola semua tenant dari satu tempat

---

## 🛠 Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 11, PHP 8.3+ |
| Admin Panel | Filament PHP 3.x |
| Frontend | Livewire 3, Alpine.js, Tailwind CSS |
| Database | MySQL 8.0 |
| Queue | Redis + Laravel Horizon |
| Pembayaran | Midtrans + Xendit |
| WhatsApp | Fonnte API |
| Multi-Tenancy | spatie/laravel-multitenancy |
| Roles | spatie/laravel-permission |
| QR Code | endroid/qr-code |
| Excel | maatwebsite/excel |
| PDF | barryvdh/laravel-dompdf |

---

## 📋 Persyaratan

- PHP 8.3+
- MySQL 8.0+
- Redis
- Composer 2.x
- Node.js 20+
- Web server: Nginx atau Apache

---

## 🚀 Instalasi Cepat

```bash
# 1. Clone repository
git clone git@github.com:monkey-one/school-system-saas.git
cd school-system-saas

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Konfigurasi
cp .env.example .env
php artisan key:generate

# 4. Setup database
php artisan migrate --seed

# 5. Data demo (opsional)
php artisan db:seed --class=DemoSeeder

# 6. Jalankan queue worker
php artisan horizon

# 7. Jalankan server
php artisan serve
```

**Demo Akses:**
- Landing: `http://localhost:8000`
- Super Admin: `http://localhost:8000/super-admin` — `superadmin@edusaas.id` / `password`
- Admin Sekolah: `http://localhost:8000/school` — `admin@smpn1demo.id` / `password`
- Guru: `http://localhost:8000/teacher` — `guru@smpn1demo.id` / `password`

Lihat `docs/INSTALLATION.md` untuk panduan lengkap.

---

## 📁 Struktur Modul

| Modul | Deskripsi |
|-------|-----------|
| Foundation | Multi-tenancy, auth, 3 Filament panels, roles |
| Akademik | Tahun ajaran, semester, kelas, mapel, kurikulum |
| Kesiswaan | PPDB online, profil siswa, alumni |
| Kepegawaian | Profil guru, jadwal mengajar |
| Absensi | QR Code attendance, GPS check-in guru |
| Penilaian | Gradebook, rapor digital Kurikulum Merdeka |
| Keuangan | SPP, pembayaran online, laporan |
| Komunikasi | Pengumuman, WhatsApp blast, pesan |
| Perpustakaan | Katalog buku, peminjaman, denda |
| Inventaris | Aset, fasilitas, booking |
| Super Admin | Manajemen tenant, langganan, analytics |
| Landing Page | Halaman marketing |
| Portal | Portal siswa & orang tua |
| REST API | API dengan Sanctum auth |

---

## 📞 Dukungan

- Dokumentasi: folder `docs/`
- Dukungan: via Codester atau email

---

## 📄 Lisensi

Regular License — untuk 1 end product (SaaS Anda).

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
