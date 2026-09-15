# EduSaaS — Panduan Developer & Teknis

Dokumen ini ditujukan untuk developer atau tim IT yang memasang, mengembangkan, dan merawat EduSaaS: struktur kode, database, konfigurasi, dan deploy ke server produksi.

> Dibuat oleh **numintek — PT Danum Inovasi Teknologi** · https://numintek.com

---

## 1. Ringkasan Arsitektur

| Lapisan | Teknologi |
|---|---|
| Framework | Laravel 11 (PHP 8.2+, disarankan 8.3/8.4) |
| Panel admin | Filament 3.3 + Livewire 3 |
| Portal siswa/ortu & website | Blade + Tailwind (CDN) + Alpine.js |
| Database | MySQL 8 / MariaDB 10.6+ |
| Antrean | Laravel Queue (driver `database`, bisa `redis`) |
| Multi-tenant | Satu database, kolom `tenant_id` + global scope |
| Hak akses | spatie/laravel-permission |
| PDF | barryvdh/laravel-dompdf |
| Excel/CSV | Filament Import/Export + maatwebsite/excel |
| API | Laravel Sanctum (REST, dokumentasi Swagger) |

### 1.1 Area aplikasi

| URL | Pengguna | Kode |
|---|---|---|
| `/` | Landing page SaaS | `LandingController`, `resources/views/landing` |
| `/register` | Daftar sekolah baru (trial) | `RegistrationController` |
| `/edusaas-admin` | **Login tunggal semua peran** + panel Admin/Operator sekolah | `app/Filament/SchoolAdmin` |
| `/super-admin` | Pemilik SaaS | `app/Filament/SuperAdmin` |
| `/teacher` | Guru | `app/Filament/Teacher` |
| `/student-portal` | Siswa | `StudentPortalController`, `resources/views/portal` |
| `/parent-portal` | Orang tua | `ParentPortalController` |
| `/profile` | Website profil sekolah | `WebsiteController`, `resources/views/website` |
| `/ppdb` | Pendaftaran siswa baru | `PPDBController` |
| `/alumni` | Direktori alumni | `AlumniController` |
| `/display` | Layar TV lobi | `DisplayController` |
| `/api/v1/*` | REST API | `routes/api.php`, `app/Http/Controllers/Api` |

Setelah login, `App\Filament\Pages\Auth\Login` mengarahkan pengguna sesuai `UserType`: super admin, admin/operator, guru, siswa, atau orang tua.

### 1.2 Multi-tenant

- `App\Models\Tenant` menyimpan sekolah aktif (`Tenant::current()`).
- Trait `App\Traits\BelongsToTenant`:
  - menambahkan global scope `tenant_id = tenant aktif`;
  - mengisi `tenant_id` otomatis saat membuat data.
- Middleware `ResolveTenant` menentukan sekolah dengan urutan berikut:
  1. **Pengguna yang login** selalu dikunci ke `tenant_id` miliknya. Super admin dapat masuk ke sekolah lewat fitur *impersonate*.
  2. Subdomain `<slug>.<APP_BASE_DOMAINS>`.
  3. Parameter `?tenant=<slug>`.
  4. `APP_DEFAULT_TENANT` untuk instalasi satu sekolah.
- `EnsureTenantIsSet` menolak request panel yang tidak memiliki sekolah.
- Antrean: `App\Support\QueueTenancy` menitipkan `tenant_id` ke setiap job dan memulihkannya saat job dijalankan worker. Karena itu import/export, notifikasi, dan tagihan otomatis berjalan di sekolah yang benar.

> **Aturan penting:** setiap model data sekolah wajib memakai `BelongsToTenant`. Jangan memakai `withoutGlobalScopes()` di kode yang dapat diakses pengguna sekolah.

### 1.3 Struktur folder penting

```
app/
  Console/Commands/     Perintah artisan (tagihan bulanan, pengingat SPP, demo reset)
  Enums/                Status & tipe (UserType, AttendanceStatus, PaymentStatus, ...)
  Filament/
    SchoolAdmin/        Resource & halaman panel sekolah
    SuperAdmin/         Resource & halaman pemilik SaaS
    Teacher/            Resource & halaman guru
    Imports/ Exports/   Import/Export CSV-Excel (siswa, guru, mapel, jadwal, nilai)
    Actions/            Aksi bersama (presensi QR, izin, pesan)
  Http/
    Controllers/        Portal, website, PPDB, pembayaran, API
    Middleware/         ResolveTenant, EnsureTenantIsSet, SecurityHeaders, EnsureUserType
  Jobs/                 Tagihan bulanan, pengingat, WhatsApp, notifikasi
  Models/               Eloquent (hampir semuanya BelongsToTenant)
  Services/             Logika bisnis (Rapor, Midtrans, Xendit, Fonnte, Ujian, Tabungan, Izin)
  Support/              Demo, SafeHtml, QueueTenancy, Geo
config/demo.php         Mode demo & kredit pengembang
database/migrations     Skema database
database/seeders        RolesAndPermissionsSeeder, DemoSeeder
lang/id.json, en.json   Terjemahan (Bahasa Indonesia default)
resources/views         Blade: portal, website, ppdb, landing, pdf, filament
routes/web.php          Rute web; routes/api.php rute API; routes/console.php jadwal
tests/Feature           Pengujian otomatis (PHPUnit)
```

---

## 2. Kebutuhan Server

- PHP 8.2+ dengan ekstensi `bcmath, ctype, curl, dom, fileinfo, gd, intl, mbstring, openssl, pdo_mysql, tokenizer, xml, zip`
- MySQL 8.0+ atau MariaDB 10.6+
- Composer 2
- Nginx (disarankan) atau Apache
- Supervisor (untuk queue worker) dan cron
- Node.js **tidak wajib**: tampilan publik memakai Tailwind CDN, dan aset Filament sudah dipublikasikan di `public/`.
- Minimal 1 vCPU / 2 GB RAM untuk ±1.000 siswa.

---

## 3. Instalasi

```bash
# 1. Ekstrak source code, lalu masuk ke foldernya
cd /var/www/edusaas

# 2. Dependensi PHP
composer install --no-dev --optimize-autoloader

# 3. Konfigurasi
cp .env.example .env
php artisan key:generate
# Edit .env: APP_URL, DB_*, APP_ENV=production, APP_DEBUG=false

# 4. Database
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder --force
# Opsional (data contoh lengkap, JANGAN di server sekolah sungguhan):
# php artisan db:seed --class=DemoSeeder --force

# 5. Storage & cache
php artisan storage:link
php artisan filament:assets
php artisan optimize

# 6. Hak akses folder
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Buat akun super admin pertama dengan `DemoSeeder`, atau lewat tinker:

```bash
php artisan tinker
>>> \App\Models\User::create(['name'=>'Super Admin','email'=>'owner@domain.com','password'=>'PasswordKuat123','type'=>\App\Enums\UserType::SUPER_ADMIN,'is_active'=>true]);
```

Setelah itu buat sekolah (tenant) dari panel **Super Admin → Sekolah**, atau lewat halaman `/register`.

### 3.1 Nginx

```nginx
server {
    listen 80;
    server_name sekolah.com *.sekolah.com;
    root /var/www/edusaas/public;
    index index.php;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Hanya index.php yang boleh dieksekusi
    location = /index.php {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
    location ~ \.php$ { return 404; }

    # Tolak file tersembunyi (.env, .git)
    location ~ /\.(?!well-known) { deny all; }
}
```

Untuk subdomain per sekolah, arahkan DNS wildcard `*.sekolah.com` ke server, gunakan sertifikat wildcard (mis. certbot DNS challenge), lalu isi `APP_BASE_DOMAINS=sekolah.com`.

**Memasang di sub-folder** (mis. `https://domain.com/edusaas/`):
- Isi `APP_URL=https://domain.com/edusaas`.
- Di Nginx, hapus prefix dari `REQUEST_URI` sebelum diteruskan ke PHP, lalu kirim header `X-Forwarded-Prefix /edusaas`. Aplikasi sudah mempercayai header ini, sehingga signed URL tetap valid.

### 3.2 Queue worker (Supervisor)

`/etc/supervisor/conf.d/edusaas-worker.conf`:

```ini
[program:edusaas-worker]
command=php /var/www/edusaas/artisan queue:work --sleep=3 --tries=3 --max-time=3600
user=www-data
numprocs=1
autostart=true
autorestart=true
stopwaitsecs=3600
redirect_stderr=true
stdout_logfile=/var/www/edusaas/storage/logs/worker.log
```

```bash
supervisorctl reread && supervisorctl update && supervisorctl start edusaas-worker
```

Worker **wajib** berjalan. Tanpa worker, import/export Excel, notifikasi WhatsApp, tagihan bulanan, dan broadcast tidak akan diproses.

### 3.3 Cron (scheduler)

```cron
* * * * * cd /var/www/edusaas && php artisan schedule:run >> /dev/null 2>&1
```

| Jadwal | Perintah | Fungsi |
|---|---|---|
| Tanggal 1, 00:30 | `edusaas:generate-monthly-bills` | Membuat tagihan SPP bulanan (dengan potongan) |
| Senin, 08:00 | `edusaas:send-spp-reminders` | Pengingat tunggakan ke orang tua (WhatsApp) |
| Tiap 6 jam (demo saja) | `edusaas:demo-reset` | Mengembalikan data demo |

---

## 4. Konfigurasi `.env`

| Variabel | Keterangan |
|---|---|
| `APP_URL` | URL lengkap tanpa garis miring di akhir |
| `APP_BASE_DOMAINS` | Domain utama SaaS (dipisah koma) |
| `APP_DEFAULT_TENANT` | Slug sekolah jika tanpa subdomain |
| `FILESYSTEM_DISK=local` | File privat (dokumen PPDB, surat izin, tugas) tidak dapat diakses publik |
| `FILAMENT_FILESYSTEM_DISK=public` | Gambar publik (logo, foto website) |
| `QUEUE_CONNECTION` | `database` (default) atau `redis` |
| `FONNTE_API_TOKEN` | Token WhatsApp gateway Fonnte |
| `MIDTRANS_*` | Pembayaran online (QRIS, VA, e-wallet). Kosongkan untuk menonaktifkan. |
| `XENDIT_*` | Alternatif gateway pembayaran |
| `DEMO_MODE` | `true` **hanya** untuk demo publik |
| `APP_AUTHOR_*` | Kredit pengembang di halaman publik |

Webhook pembayaran:
- Midtrans: `POST {APP_URL}/webhooks/midtrans`
- Xendit: `POST {APP_URL}/webhooks/xendit`

Tanda tangan dan token diverifikasi server, dan webhook ditolak jika kunci belum diisi.

---

## 5. Database

Skema lengkap ada di `database/migrations` (lihat juga `docs/DATABASE_SCHEMA.md`). Kelompok tabel utama:

| Kelompok | Tabel |
|---|---|
| SaaS | `tenants`, `plans`, `subscriptions`, `users`, `roles`, `permissions`, `activity_log` |
| Akademik | `academic_years`, `semesters`, `grade_levels`, `classrooms`, `subjects`, `classroom_subjects`, `teaching_schedules`, `curriculum_settings` |
| SDM & siswa | `teachers`, `students`, `student_parents`, `alumni`, `teacher_attendances` |
| Presensi | `attendance_sessions`, `student_attendances`, `leave_requests` |
| Penilaian | `assessment_types`, `assessments`, `student_grades`, `report_cards` |
| E-learning | `assignments`, `assignment_submissions`, `exams`, `exam_questions`, `exam_attempts` |
| Keuangan | `spp_types`, `spp_discounts`, `spp_bills`, `payments`, `payment_bill_allocations`, `savings_transactions` |
| Kesiswaan | `violation_types`, `student_violations`, `counseling_notes`, `extracurriculars`, `student_extracurriculars`, `achievements` |
| PPDB | `ppdb_waves`, `ppdb_registrations` |
| Website | `posts`, `school_events`, `gallery_albums`, `gallery_items`, `announcements` |
| Sarpras | `books`, `book_loans`, `assets`, `facilities`, `facility_bookings` |
| Komunikasi | `messages`, `notification_templates`, `whatsapp_logs`, `notifications` |
| Sistem | `jobs`, `failed_jobs`, `job_batches`, `imports`, `exports`, `cache`, `sessions` |

Semua tabel data sekolah memiliki kolom `tenant_id` (foreign key ke `tenants`, *cascade on delete*).

**Backup harian** yang disarankan:

```bash
mysqldump --single-transaction -u edusaas -p edusaas | gzip > /backup/edusaas-$(date +%F).sql.gz
tar czf /backup/edusaas-storage-$(date +%F).tgz -C /var/www/edusaas storage/app
```

---

## 6. Keamanan (sudah diterapkan)

- Isolasi data antar-sekolah lewat global scope. Pengguna yang login dikunci ke sekolahnya, termasuk pada request Livewire.
- Rate limit untuk login, API, formulir publik (PPDB, kontak), registrasi, presensi QR, dan webhook.
- Header keamanan global: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`.
- Upload dibatasi tipe dan ukurannya. File privat disimpan di disk `local` dan dilayani lewat controller dengan cek hak akses, bukan URL publik.
- Konten HTML dari editor disanitasi (`App\Support\SafeHtml`) sebelum ditampilkan.
- Kunci jawaban ujian tidak pernah dikirim ke browser, dan batas waktu dicek di server.
- Webhook pembayaran memverifikasi tanda tangan dan nominal.
- Mode demo menonaktifkan perubahan akun, penghapusan data inti, pengaturan sistem, broadcast WhatsApp, dan upload file siswa.

**Checklist produksi:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DEMO_MODE=false`
- HTTPS aktif
- `SESSION_SECURE_COOKIE=true`
- Ganti password super admin
- PHP-FPM berjalan dengan user khusus, dan `open_basedir` disarankan

---

## 7. Pengembangan

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed --seeder=DemoSeeder
php artisan serve             # http://localhost:8000
php artisan queue:work        # terminal kedua
```

Akun demo (password `password`):
- `superadmin@edusaas.id`
- `admin@smpn1demo.id`
- `operator@smpn1demo.id`
- `guru@smpn1demo.id`
- `siswa@smpn1demo.id`
- `ortu@smpn1demo.id`

### 7.1 Pengujian

```bash
cp .env .env.testing   # set DB_DATABASE=edusaas_test
php artisan test
```

### 7.2 Menambah modul baru (pola)

1. Buat migrasi dengan `foreignId('tenant_id')->constrained()->cascadeOnDelete()`.
2. Buat model dengan `use BelongsToTenant;` dan isi `$fillable`.
3. Buat resource Filament di `app/Filament/SchoolAdmin/Resources`. Resource ditemukan otomatis.
4. Tambahkan permission di `RolesAndPermissionsSeeder` jika perlu.
5. Tambahkan teks ke `lang/id.json` dan `lang/en.json` (kunci bahasa Inggris, nilai Indonesia).
6. Tulis test di `tests/Feature`.

### 7.3 Terjemahan

Semua teks antarmuka memakai `__('English text')`. Terjemahan Indonesia ada di `lang/id.json`. Bahasa default diatur oleh `APP_LOCALE=id`, dan pengguna dapat mengganti bahasa lewat tombol ID/EN.

### 7.4 Kustomisasi tampilan

Lihat `docs/CUSTOMIZATION.md`. Warna panel diatur di `app/Providers/Filament/*PanelProvider.php`. Warna portal dan website diatur di konfigurasi Tailwind pada layout Blade masing-masing.

---

## 8. Update Aplikasi

```bash
php artisan down
# salin file versi baru (jangan timpa .env dan storage/)
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder --force
php artisan optimize:clear && php artisan optimize
php artisan queue:restart
php artisan up
```

---

## 9. Pemecahan Masalah

| Gejala | Solusi |
|---|---|
| Import/Export tidak selesai | Pastikan queue worker berjalan (`supervisorctl status`), lalu cek `failed_jobs` |
| Halaman 404 setelah deploy | Jalankan `php artisan optimize:clear`, lalu `php artisan optimize` |
| Gambar tidak tampil | Jalankan `php artisan storage:link` dan periksa `FILAMENT_FILESYSTEM_DISK=public` |
| Link unduhan "Invalid signature" di sub-folder | Kirim header `X-Forwarded-Prefix` dari Nginx |
| Tagihan bulanan tidak terbuat | Periksa cron `schedule:run`, lalu jalankan manual `php artisan edusaas:generate-monthly-bills` |
| WhatsApp tidak terkirim | Isi `FONNTE_API_TOKEN`, pastikan perangkat Fonnte terhubung, lalu cek menu *Log WhatsApp* |
| Error 419 (page expired) | Samakan `SESSION_DOMAIN` dan `APP_URL`, lalu aktifkan HTTPS secara konsisten |

---

## 10. Dukungan

Instalasi, kustomisasi, hosting, dan pengembangan fitur tersedia melalui **numintek — PT Danum Inovasi Teknologi**:
- https://numintek.com
- WhatsApp +62 852-2009-1770
