# Panduan Pengguna EduSaaS

## Daftar Isi

1. [Super Admin](#1-super-admin)
2. [Admin Sekolah](#2-admin-sekolah)
3. [Guru](#3-guru)
4. [Siswa](#4-siswa)
5. [Orang Tua](#5-orang-tua)

---

## 1. Super Admin

Super Admin mengelola seluruh platform EduSaaS, termasuk tenant (sekolah), paket langganan, dan monitoring sistem.

**Akses:** `/super-admin`

### 1.1 Manajemen Tenant (Sekolah)

- **Tambah Sekolah Baru:** Klik "Tambah" → isi nama sekolah, slug (subdomain), tipe sekolah, dan data kontak.
- **Lihat Detail:** Klik nama sekolah untuk melihat detail lengkap termasuk jumlah siswa, guru, dan status langganan.
- **Ubah Status:** Aktifkan, tangguhkan, atau nonaktifkan sekolah sesuai kebutuhan.

### 1.2 Manajemen Paket (Plan)

- **Kelola Paket:** Buat dan edit paket langganan dengan harga bulanan/tahunan, batas siswa/guru, dan fitur yang disertakan.
- **Fitur Paket:** Tentukan modul apa saja yang tersedia per paket (absensi, SPP, PPDB, perpustakaan, dll).

### 1.3 Manajemen Langganan

- **Monitor Status:** Lihat semua langganan aktif, yang mendekati kadaluarsa, dan yang sudah expired.
- **Perpanjang Manual:** Admin dapat memperpanjang langganan secara manual jika diperlukan.

### 1.4 Monitoring Sistem

- **Dashboard:** Statistik tenant aktif, total siswa, total guru, dan pendapatan.
- **Log Aktivitas:** Pantau aktivitas penting di platform.

---

## 2. Admin Sekolah

Admin Sekolah mengelola semua aspek operasional sekolah.

**Akses:** `/school-admin`

### 2.1 Dashboard

Menampilkan ringkasan:
- Jumlah siswa, guru, dan kelas aktif
- Kehadiran hari ini
- Tagihan SPP yang jatuh tempo
- Pengumuman terbaru

### 2.2 Tahun Ajaran & Semester

- Buat tahun ajaran baru (misal: 2025/2026)
- Kelola semester (Ganjil/Genap) dengan tanggal mulai dan berakhir
- Aktifkan semester yang sedang berjalan

### 2.3 Kelas & Tingkat

- **Tingkat Kelas:** Kelola tingkat (Kelas 7, 8, 9)
- **Rombongan Belajar:** Buat kelas (7A, 7B, dst) dengan kapasitas dan wali kelas
- **Penempatan Siswa:** Assign siswa ke kelas melalui menu kelas

### 2.4 Mata Pelajaran

- Tambah mata pelajaran dengan kode, tipe (Teori/Praktek/Muatan Lokal)
- Assign guru ke mata pelajaran per kelas

### 2.5 Manajemen Guru

- Daftar guru lengkap dengan NIP, status kepegawaian, dan data personal
- Buat akun login otomatis untuk setiap guru
- Assign sebagai wali kelas

### 2.6 Manajemen Siswa

- Data siswa lengkap: NIS, NISN, data pribadi, orang tua
- Import data siswa via Excel
- Mutasi siswa (pindah, keluar, alumni)

### 2.7 PPDB (Penerimaan Peserta Didik Baru)

- **Buat Gelombang:** Tentukan kuota, tanggal buka/tutup, dan persyaratan
- **Kelola Pendaftar:** Terima, tolak, atau masukkan daftar tunggu
- **Formulir Online:** Orang tua mengisi di halaman publik `/ppdb`
- **Cek Status:** Pendaftar bisa cek status di `/ppdb/status`

### 2.8 Kehadiran

- **Buat Sesi Absensi:** Buat sesi per mata pelajaran, generate QR code
- **Input Manual:** Catat kehadiran secara manual jika diperlukan
- **Rekap:** Lihat rekap kehadiran per siswa, per kelas, per periode
- **Notifikasi Orang Tua:** Kirim notifikasi WhatsApp otomatis jika siswa alfa

### 2.9 Penilaian & Rapor

- **Tipe Penilaian:** Kelola bobot nilai (Tugas, UH, PTS, PAS, Proyek)
- **Input Nilai:** Guru menginput nilai per assessment
- **Rapor:** Generate rapor otomatis berdasarkan nilai yang sudah diinput
- **Cetak Rapor:** Unduh rapor dalam format PDF

### 2.10 Keuangan (SPP)

- **Tipe SPP:** Buat tipe pembayaran (SPP Bulanan, Uang Gedung, dll)
- **Generate Tagihan:** Buat tagihan bulanan untuk seluruh siswa
- **Catat Pembayaran:** Input pembayaran manual (tunai/transfer)
- **Gateway Online:** Terima pembayaran via Midtrans atau Xendit
- **Laporan:** Laporan pembayaran, tunggakan, dan rekap keuangan
- **Pengingat:** Kirim pengingat SPP via WhatsApp

### 2.11 Perpustakaan

- Kelola katalog buku dan peminjaman
- Catat pengembalian dan denda keterlambatan

### 2.12 Aset & Inventaris

- Kelola aset sekolah: elektronik, furnitur, laboratorium
- Catat kondisi dan lokasi aset

### 2.13 Pengumuman

- Buat pengumuman untuk semua, per kelas, atau per grup
- Pin pengumuman penting
- Atur tanggal kadaluarsa

### 2.14 Template Notifikasi

- Kelola template pesan WhatsApp
- Gunakan variabel: `{{student_name}}`, `{{period}}`, `{{amount}}`, dll

---

## 3. Guru

Guru mengakses fitur terkait kelas dan mata pelajaran yang diajar.

**Akses:** `/teacher`

### 3.1 Dashboard

- Jadwal mengajar hari ini
- Kelas yang diampu
- Notifikasi terbaru

### 3.2 Kehadiran

- Buat sesi absensi untuk kelas yang diajar
- Generate QR code untuk absensi
- Input kehadiran manual
- Lihat rekap kehadiran

### 3.3 Penilaian

- Buat assessment (Tugas, UH, PTS, PAS)
- Input nilai per siswa
- Lihat rekap nilai per mata pelajaran

### 3.4 Wali Kelas (jika ditunjuk)

- Lihat data semua siswa di kelas
- Rekap kehadiran seluruh mata pelajaran
- Monitor nilai siswa dari semua guru
- Input/verifikasi rapor

### 3.5 Pengumuman

- Lihat pengumuman dari sekolah

---

## 4. Siswa

Siswa mengakses portal siswa untuk informasi akademik.

**Akses:** `/student-portal`

### 4.1 Dashboard

- Jadwal pelajaran
- Pengumuman terbaru
- Ringkasan kehadiran dan nilai

### 4.2 Kehadiran

- Scan QR untuk absensi
- Lihat riwayat kehadiran

### 4.3 Nilai

- Lihat nilai per mata pelajaran
- Unduh rapor (jika sudah tersedia)

### 4.4 SPP

- Lihat tagihan SPP
- Bayar online via Midtrans/Xendit
- Riwayat pembayaran

### 4.5 Pengumuman

- Lihat pengumuman dari sekolah

---

## 5. Orang Tua

Orang tua mengakses portal untuk memonitor anak.

**Akses:** `/parent-portal`

### 5.1 Dashboard

- Ringkasan data anak (bisa lebih dari satu)
- Kehadiran hari ini
- Tagihan SPP terbaru

### 5.2 Kehadiran

- Lihat kehadiran anak per hari/bulan
- Terima notifikasi WhatsApp jika anak alfa

### 5.3 Nilai

- Lihat nilai anak per mata pelajaran
- Unduh rapor

### 5.4 SPP

- Lihat tagihan SPP anak
- Bayar online
- Riwayat pembayaran

### 5.5 Pesan

- Terima pesan dari sekolah/guru
- Notifikasi WhatsApp untuk informasi penting

---

## Tips Umum

- **Pencarian:** Gunakan kolom pencarian di setiap tabel untuk menemukan data dengan cepat
- **Filter:** Manfaatkan filter untuk mempersempit tampilan data
- **Ekspor:** Hampir semua tabel bisa diekspor ke Excel
- **Bahasa:** Aplikasi mendukung Bahasa Indonesia dan Bahasa Inggris
- **Responsif:** Aplikasi bisa diakses dari HP, tablet, dan komputer
