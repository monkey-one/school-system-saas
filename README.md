# EduSaaS — Sistem Manajemen Sekolah (Multi-Sekolah / SaaS)

> Laravel 11 · Filament 3 · Livewire 3 · MySQL — by **numintek (PT Danum Inovasi Teknologi)**

EduSaaS adalah sistem manajemen sekolah lengkap yang dapat dipakai untuk **satu sekolah**, atau dijalankan sebagai **SaaS untuk banyak sekolah**. Setiap sekolah memiliki data terisolasi, website profil, halaman PPDB, portal siswa, dan portal orang tua. Antarmuka tersedia dalam Bahasa Indonesia dan English.

**Live demo:** https://numintek.com/edusaas/

---

## ✨ Fitur Utama

| Area | Fitur |
|---|---|
| SaaS | Multi-tenant (subdomain/`?tenant=`), paket & langganan, registrasi trial, impersonate sekolah, analitik platform |
| Akademik | Tahun ajaran, semester, kelas, mapel, jadwal, penilaian berbobot, rapor PDF, kenaikan kelas, kelulusan → alumni |
| Import/Export | Siswa, guru, mapel, jadwal mengajar, nilai (Excel/CSV) |
| Presensi | QR code per jam pelajaran, presensi GPS guru, izin/sakit online (otomatis mengisi presensi), notifikasi WA ketidakhadiran |
| E-Learning | Tugas online (teks/file, nilai & umpan balik), ujian online pilihan ganda (timer, autosave, penilaian otomatis), sinkron ke buku nilai |
| Keuangan | Tagihan SPP otomatis + potongan, pembayaran tunai/online (Midtrans, Xendit), kwitansi PDF, pengingat WA, laporan keuangan, tabungan siswa/cashless |
| Kesiswaan | Poin pelanggaran, catatan BK, prestasi, ekstrakurikuler, perpustakaan, aset, peminjaman fasilitas |
| PPDB | Gelombang & kuota, formulir + unggah dokumen, cek status, verifikasi, daftarkan sebagai siswa sekali klik |
| Website | Beranda, profil, berita, agenda, prestasi, galeri, guru, kontak, sitemap, layar TV lobi |
| Portal | Portal siswa & portal orang tua (multi-anak) |
| Komunikasi | Pengumuman, pesan internal, broadcast & log WhatsApp (Fonnte), template notifikasi |
| API | REST API (Sanctum) + Swagger |

---

## 🚀 Instalasi Singkat

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
# atur APP_URL dan DB_* di .env

php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder --force
php artisan db:seed --class=DemoSeeder --force   # opsional: data contoh

php artisan storage:link
php artisan optimize

php artisan queue:work    # wajib berjalan (gunakan Supervisor di server)
```

Tambahkan cron berikut:

```cron
* * * * * cd /path/to/edusaas && php artisan schedule:run >> /dev/null 2>&1
```

Panduan lengkap (Nginx, subdomain, sub-folder, Supervisor, keamanan, backup, update) ada di **[docs/DEVELOPER_GUIDE.md](docs/DEVELOPER_GUIDE.md)**.

---

## 🔑 Akun Demo (setelah `DemoSeeder`)

Semua peran masuk dari **`/edusaas-admin/login`**, lalu diarahkan otomatis ke panel atau portal masing-masing. Password semua akun: `password`.

| Peran | Email |
|---|---|
| Super Admin | superadmin@edusaas.id |
| Admin Sekolah | admin@smpn1demo.id |
| Operator | operator@smpn1demo.id |
| Guru | guru@smpn1demo.id |
| Siswa | siswa@smpn1demo.id |
| Orang Tua | ortu@smpn1demo.id |

> Di server produksi, jangan jalankan `DemoSeeder`. Atau ganti semua password segera setelah instalasi.

---

## 📚 Dokumentasi

| Dokumen | Isi |
|---|---|
| [docs/USER_GUIDE.md](docs/USER_GUIDE.md) | Panduan pengguna: super admin, admin/operator, guru, siswa, orang tua |
| [docs/DEVELOPER_GUIDE.md](docs/DEVELOPER_GUIDE.md) | Arsitektur, database, instalasi, deploy, keamanan, pemecahan masalah |
| [docs/DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) | Rincian skema database |
| [docs/CUSTOMIZATION.md](docs/CUSTOMIZATION.md) | Kustomisasi tampilan & fitur |

---

## 🛠 Kebutuhan

- PHP 8.2+ (ekstensi: bcmath, ctype, curl, dom, fileinfo, gd, intl, mbstring, openssl, pdo_mysql, xml, zip)
- MySQL 8.0+ / MariaDB 10.6+
- Composer 2
- Nginx atau Apache, cron, Supervisor

---

## 🤝 Dukungan & Kustomisasi

Instalasi, hosting, pelatihan, fitur khusus, dan SaaS white-label:

**numintek — PT Danum Inovasi Teknologi**
- https://numintek.com
- WhatsApp +62 852-2009-1770

## 📄 Lisensi

Digunakan sesuai lisensi yang dibeli di Codester. Dilarang mendistribusikan ulang source code.
