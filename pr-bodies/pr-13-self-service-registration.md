# PR #13: Fitur Pendaftaran Sekolah Self-Service dengan Pembayaran Midtrans

## Ringkasan
Menambahkan fitur pendaftaran sekolah secara mandiri (self-service) yang memungkinkan pemilik/admin sekolah mendaftar, memilih paket langganan, dan melakukan pembayaran online melalui Midtrans Snap. Fitur ini mencakup opsi uji coba gratis 14 hari dan langganan berbayar (bulanan/tahunan dengan diskon 20%).

## Perubahan Utama

### Backend
- **RegistrationController** (BARU): Controller lengkap dengan 5 method:
  - `create()` — Form pendaftaran multi-step (pilih paket → info sekolah → akun admin)
  - `store()` — Validasi, buat tenant (TRIAL/SUSPENDED), user school_admin, subscription, dan generate Midtrans Snap token
  - `payment()` — Halaman pembayaran dengan Midtrans Snap embed
  - `success()` — Halaman konfirmasi setelah pembayaran berhasil
  - `trialSuccess()` — Halaman konfirmasi uji coba gratis aktif

- **PaymentWebhookController** (DIPERBARUI): Tambah method `midtransSubscription()` untuk handle webhook pembayaran langganan — verifikasi signature, update subscription ke active, aktifkan tenant

- **Subscription Model** (DIPERBARUI): Tambah field fillable baru (`order_id`, `snap_token`, `midtrans_transaction_id`, `billing_cycle`, `amount`)

- **Migration** (BARU): Tambah kolom payment fields ke tabel subscriptions

- **Routes** (DIPERBARUI): Tambah 5 route pendaftaran + 1 route webhook subscription

### Frontend
- **Form Pendaftaran** (`registration/index.blade.php`): Multi-step wizard dengan Alpine.js
  - Step 1: Pilih tipe (trial/berbayar), toggle bulanan/tahunan, pilih paket
  - Step 2: Info sekolah (nama, jenis, NPSN, kota, provinsi, telepon, email, kepala sekolah)
  - Step 3: Akun administrator (nama, email, password) + ringkasan

- **Halaman Pembayaran** (`registration/payment.blade.php`): Order summary + Midtrans Snap payment popup

- **Halaman Sukses** (`registration/success.blade.php`): Konfirmasi pembayaran berhasil + link login

- **Halaman Trial Sukses** (`registration/trial-success.blade.php`): Konfirmasi trial aktif + info masa trial + link login

- **Landing Page** (DIPERBARUI): Semua tombol CTA ("Coba Gratis", "Mulai Gratis", dll.) sekarang mengarah ke halaman pendaftaran

### Terjemahan
- Tambah 69 kunci terjemahan baru (EN/ID) untuk semua halaman pendaftaran

## Detail Teknis
- Paket tersedia: Starter (Rp500K/bln), Professional (Rp1M/bln), Enterprise (Rp2M/bln) — diskon 20% untuk tahunan
- Midtrans Snap (sandbox mode) untuk pembayaran
- Tenant dibuat dengan status TRIAL (14 hari) untuk uji coba, atau SUSPENDED untuk berbayar (diaktifkan via webhook)
- User school_admin dibuat langsung aktif untuk trial, menunggu pembayaran untuk berbayar
- CSRF exclusion sudah cover route webhook baru (`webhooks/*`)

## Dampak
- User baru bisa mendaftar secara mandiri tanpa harus dihubungi admin
- Proses pendaftaran otomatis dari form sampai aktivasi akun
- Landing page sekarang mengarahkan pengunjung ke form pendaftaran
