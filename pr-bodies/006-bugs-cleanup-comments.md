# Perbaikan Bug Kritis, Patch Keamanan, dan Komentar Kode

## Ringkasan
PR ini berisi perbaikan 10+ bug kritis, patch keamanan, penghapusan dead code, eksternalisasi konfigurasi hardcode, serta penambahan komentar bahasa Inggris ke seluruh 136 file dalam proyek.

## Perbaikan Bug

### Kritis
- **PPDBController**: Menambahkan null guard untuk tenant (sebelumnya crash jika tenant tidak ditemukan). Membungkus pembuatan nomor registrasi dalam `DB::transaction()` dengan `lockForUpdate()` untuk mencegah race condition dan duplikasi nomor
- **WhatsAppService**: Memperbaiki penggantian variabel template dari `{key}` menjadi `{{key}}` agar sesuai dengan format template notifikasi di database
- **SendOverdueSppReminders**: Memperbaiki nama variabel template yang tidak cocok (`nama_siswa` -> `student_name`, `periode` -> `period`, `jumlah` -> `amount`, `jatuh_tempo` -> `due_date`)
- **NotifyParentAbsentStudent**: Memperbaiki nama variabel template yang tidak cocok (`nama_siswa` -> `student_name`, `tanggal` -> `date`, `kelas` -> `subject`)

### Keamanan
- **Routes Attendance**: Menambahkan middleware `ResolveTenant` ke route attendance scan/confirm (sebelumnya tanpa middleware sama sekali, memungkinkan injeksi absensi lintas tenant)

### Bug Lainnya
- **PaymentWebhookController**: Akses telepon orang tua yang null-safe (`$parent->phone` -> `$parent?->phone`)
- **ParentPortalController**: Memperbaiki query pesan yang rusak dari `JSON_CONTAINS(?, CAST(sender_id AS CHAR))` menjadi `JSON_CONTAINS(recipients, ?)`
- **StudentPortalController + ParentPortalController**: Mengganti `time()` dengan `uniqid()` untuk order ID pembayaran (mencegah collision dalam detik yang sama)

## Pembersihan Kode

- **User Model**: Menghapus trait `HasRoles` dari Spatie yang tidak terpakai (tabel roles kosong, otorisasi menggunakan enum `UserType`)
- **AppServiceProvider**: Menghapus override `config(['app.locale' => 'id'])` yang redundan (sudah diatur di config/app.php dan dikelola oleh middleware SetLocale)
- **ResolveTenant**: Mengeksternalisasi baseDomains ke konfigurasi via `config('app.base_domains')` sehingga domain produksi bisa ditambah via `.env` (`APP_BASE_DOMAINS=yourmoonkey.com`)
- **config/app.php**: Menambahkan key `base_domains` (dari env `APP_BASE_DOMAINS`, dipisah koma) dan `default_tenant_slug` (dari env `APP_DEFAULT_TENANT`, default 'demo')

## Komentar Kode

Menambahkan komentar bahasa Inggris ke seluruh file proyek untuk meningkatkan maintainability:
- 43 Model
- 13 Enum
- 7 API Controller
- 6 Web Controller
- 5 Service
- 5 Job
- 3 Middleware
- 3 Filament Panel Provider
- 1 Filament Login Page
- 41 Filament Resource/Widget (24 SchoolAdmin, 5 widget SchoolAdmin, 4 SuperAdmin, 3 widget SuperAdmin, 5 Teacher)
- File konfigurasi, route, bootstrap, dan trait

## Konfigurasi VPS yang Diperlukan
Setelah deploy, tambahkan ke `.env` di VPS:
```
APP_BASE_DOMAINS=yourmoonkey.com
APP_DEFAULT_TENANT=demo
```

## File yang Diubah
137 file (466 baris ditambah, 83 baris dihapus)
