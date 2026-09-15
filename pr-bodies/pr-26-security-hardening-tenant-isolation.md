# PR #26 — Security Hardening, Isolasi Tenant & Perbaikan Bug Kritis

## Ringkasan

Audit keamanan menemukan beberapa celah yang bisa dimanfaatkan untuk membaca data sekolah lain, memalsukan pembayaran, atau mengunggah file berbahaya ke server. PR ini menutup semua celah tersebut, memperbaiki alur login siswa/orang tua dan absensi QR yang sebelumnya tidak berfungsi, serta menambahkan mode demo yang aman untuk live demo publik (Codester).

## Celah Keamanan yang Ditutup

| # | Masalah | Dampak | Perbaikan |
|---|---|---|---|
| 1 | Tenant ditentukan dari URL, bukan dari akun | Admin sekolah mana pun (termasuk hasil registrasi publik) bisa melihat data sekolah `demo` | `ResolveTenant` kini mengunci user yang login ke `tenant_id` miliknya |
| 2 | Request Livewire (tabel, search, pagination, aksi) tidak menjalankan `ResolveTenant` | Query tabel Filament tidak ter-scope → data semua sekolah terlihat | Middleware tenant dijadikan *persistent* di panel School Admin & Guru |
| 3 | Route model binding berjalan sebelum tenant di-set | `{student}`, `{bill}`, `{semester}` bisa menunjuk data sekolah lain | Urutan prioritas middleware: tenant diselesaikan sebelum `SubstituteBindings` |
| 4 | Webhook Midtrans/Xendit lolos verifikasi jika key kosong | Siapa pun bisa menandai tagihan lunas / mengaktifkan langganan | Tolak jika key belum dikonfigurasi + validasi `gross_amount` |
| 5 | Upload lampiran/bukti bayar/dokumen tanpa batasan tipe | Upload `.php`/`.html` ke storage publik | `acceptedFileTypes` (PDF/gambar/dokumen office) + nginx hanya mengeksekusi `index.php` |
| 6 | Absensi QR menerima `student_id` bebas | Siapa pun bisa mengabsenkan siswa mana pun | Wajib login sebagai siswa; hanya untuk kelas sesi tersebut |
| 7 | Tidak ada rate limit | Brute force login/API, spam registrasi & PPDB | Rate limiter: `api-login`, `api`, `registration`, `public-forms`, `attendance`, `webhooks` |
| 8 | API tanpa pembatasan role & validasi relasi lintas tenant | Siswa bisa CRUD data siswa; `classroom_id` sekolah lain diterima | Route API dibatasi `user.type`, validasi `exists` ter-scope tenant |
| 9 | Halaman pembayaran registrasi bisa di-enumerate (`/register/payment/{id}`) | Bocor data sekolah + membuat token Midtrans | URL bertanda tangan (signed, 24 jam) |
| 10 | Tidak ada security header | Clickjacking, MIME sniffing | Middleware global `SecurityHeaders` |

## Perbaikan Bug

- **Login siswa & orang tua** (catatan calon client): sebelumnya diarahkan ke panel admin (403). Kini diarahkan ke Portal Siswa / Portal Orang Tua, termasuk kembali ke halaman tujuan (mis. link scan QR).
- **Absensi QR**: halaman scan sebelumnya POST JSON tanpa `student_id` dan memanggil view yang tidak ada (`attendance.success`) → selalu gagal. Ditulis ulang sebagai form server-side.
- **Enter Panel (impersonate)** di Super Admin tidak pernah bisa dipakai → kini berfungsi, dengan banner "Kembali ke Super Admin".
- **System Settings**: nama aplikasi, bahasa, zona waktu tidak tersimpan → kini tersimpan & diterapkan. Registrasi mematuhi toggle "izinkan registrasi" & "lama trial".
- **PPDB**: validasi gender tidak cocok dengan enum (`male/female` vs `L/P`) → error 500; gelombang dari sekolah lain diterima; nomor pendaftaran bentrok antar sekolah (kolom unique global).
- **Seeder**: `RolesAndPermissionsSeeder` tidak pernah dipanggil (tabel roles kosong).
- **Test suite**: 18 dari 19 test gagal karena factory tidak ada → `TenantFactory`, `StudentFactory` ditambahkan.
- Hapus 3 komponen Livewire mati yang merujuk view yang tidak ada.

## Fitur Baru Pendukung

- **Mode Demo** (`DEMO_MODE=true`, `config/demo.php`):
  - Data login akun demo tidak bisa diubah, akun/sekolah/role tidak bisa dihapus.
  - System Settings read-only.
  - `php artisan edusaas:demo-reset` + jadwal otomatis (default tiap 6 jam) — menolak jalan jika demo mode mati.
- Middleware `user.type:...` untuk membatasi route per jenis user.

## Infrastruktur VPS (di luar repo)

- Pool PHP-FPM terisolasi `edusaas` (user Linux sendiri, `open_basedir`, `disable_functions` exec/shell/proc_open, dll).
- Nginx `/edusaas/`: hanya `index.php` yang dieksekusi, `.php` & HTML di `/storage` ditolak, dotfile ditolak.
- Database user khusus hanya untuk DB `edusaas`.

## Test

`php artisan test` di VPS (DB `edusaas_test`): semua test lulus, termasuk `SecurityTest` baru (pinning tenant, webhook palsu, API lintas sekolah, absensi kelas lain, security header).

## Dampak

- **Breaking kecil**: route API siswa hanya untuk user type `student`; CRUD siswa via API hanya untuk admin/operator.
- Setelah deploy: `php artisan migrate --force`, `php artisan db:seed --class=RolesAndPermissionsSeeder --force`, `php artisan optimize`.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
