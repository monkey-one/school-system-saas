# EduSaaS — Panduan Pengguna

Panduan ini ditujukan untuk pengguna aplikasi: pemilik SaaS, admin dan operator sekolah, guru, siswa, dan orang tua.

> Dibuat oleh **numintek — PT Danum Inovasi Teknologi** · https://numintek.com

---

## Daftar Isi

1. [Masuk ke Aplikasi](#1-masuk-ke-aplikasi)
2. [Super Admin (Pemilik SaaS)](#2-super-admin-pemilik-saas)
3. [Admin & Operator Sekolah](#3-admin--operator-sekolah)
4. [Guru](#4-guru)
5. [Siswa](#5-siswa)
6. [Orang Tua](#6-orang-tua)
7. [Halaman Publik](#7-halaman-publik)
8. [Alur Kerja Penting](#8-alur-kerja-penting)
9. [Pertanyaan Umum](#9-pertanyaan-umum)

---

## 1. Masuk ke Aplikasi

Semua peran masuk dari halaman yang sama: **`/edusaas-admin/login`**. Setelah login, sistem membuka halaman sesuai peran:

| Peran | Halaman setelah login |
|---|---|
| Super Admin | Panel Super Admin |
| Admin / Operator sekolah | Panel Sekolah |
| Guru | Panel Guru |
| Siswa | Portal Siswa |
| Orang Tua | Portal Orang Tua |

- Tombol **ID / EN** mengganti bahasa.
- Siswa dan orang tua dapat mengganti password di menu **Akun**.
- Setelah 5 kali salah password, login dikunci sementara.

---

## 2. Super Admin (Pemilik SaaS)

| Menu | Fungsi |
|---|---|
| **Dashboard / Analitik Platform** | Jumlah sekolah, pengguna, langganan, dan pendapatan |
| **Sekolah (Tenant)** | Tambah, ubah, atau tangguhkan sekolah. Tombol **Masuk Panel** membuka panel sekolah tersebut (impersonate); keluar lewat banner di atas. |
| **Paket** | Paket langganan: harga, batas siswa, dan fitur |
| **Langganan** | Status langganan dan masa aktif tiap sekolah |
| **Pengguna, Role & Permission** | Akun dan hak akses |
| **Log Aktivitas** | Jejak perubahan data |
| **Pengaturan Sistem** | Nama aplikasi, kontak, dan konfigurasi umum |

---

## 3. Admin & Operator Sekolah

### 3.1 Persiapan awal (lakukan berurutan)
1. **Profil Sekolah**: nama, logo, kepala sekolah, visi-misi, kontak, konten website, dan koordinat presensi GPS guru.
2. **Tahun Ajaran & Semester**: buat lalu tandai yang aktif.
3. **Tingkat & Kelas**: kelas 7A, 7B, dan seterusnya, beserta wali kelasnya.
4. **Mata Pelajaran**: tambah manual atau **Import** dari Excel/CSV.
5. **Guru**: tambah manual atau **Import**. Akun login guru dibuat otomatis.
6. **Siswa**: tambah manual atau **Import**. Data orang tua (email) menghubungkan akun orang tua.
7. **Mapel per Kelas & Jadwal Mengajar**: pasangkan mapel, kelas, dan guru, lalu susun jadwal (bisa **Import**).
8. **Jenis Penilaian**: mis. Tugas, UH, PTS, PAS, beserta bobotnya.
9. **Keuangan**: jenis SPP/biaya, potongan, dan tanggal jatuh tempo.

### 3.2 Import & Export
Tombol **Import** dan **Export** tersedia di menu **Siswa, Guru, Mata Pelajaran, Jadwal Mengajar, dan Nilai**.
- Klik **Import**, lalu unduh **contoh file**, isi, dan unggah kembali. Proses berjalan di latar belakang, dan notifikasi (lonceng) muncul setelah selesai beserta daftar baris yang gagal.
- **Export** menghasilkan file CSV/XLSX, dengan pilihan kolom yang ingin diekspor.

### 3.3 Akademik
| Menu | Fungsi |
|---|---|
| **Sesi Presensi** | Presensi per jam pelajaran: manual, atau QR code yang dipindai siswa dari HP |
| **Presensi Guru** | Rekap presensi guru (manual, QR, atau GPS) |
| **Pengajuan Izin** | Izin/sakit dari orang tua atau siswa. **Setujui** akan mengisi presensi otomatis. |
| **Penilaian & Nilai** | Daftar penilaian dan nilai siswa (bisa Import/Export) |
| **Rapor** | **Buat Rapor** dari nilai, **Terbitkan** (satu per satu atau massal), lalu unduh PDF |
| **Kenaikan Kelas** | Pilih kelas asal dan tujuan, centang siswa, lalu naikkan sekaligus |
| **Kelulusan** | Pilih kelas akhir dan tahun lulus. Siswa menjadi alumni dan tampil di direktori alumni. |
| **Alumni** | Data alumni: melanjutkan ke mana, pekerjaan, kontak |

### 3.4 Kesiswaan
| Menu | Fungsi |
|---|---|
| **Jenis & Catatan Pelanggaran** | Poin tata tertib; bisa ditampilkan ke orang tua |
| **Catatan BK** | Konseling; catatan rahasia tidak tampil di portal |
| **Prestasi** | Prestasi siswa/sekolah; tampil di website |
| **Ekstrakurikuler** | Kegiatan dan anggota |
| **Tabungan Siswa** | Setor, tarik, dan belanja kantin (cashless). Saldo terlihat di portal. |

### 3.5 Keuangan
| Menu | Fungsi |
|---|---|
| **Tagihan SPP** | Dibuat otomatis tiap tanggal 1, atau manual |
| **Pembayaran** | Catat pembayaran tunai/transfer; pembayaran online (Midtrans/Xendit) tercatat otomatis. Kwitansi PDF tersedia. |
| **Laporan Keuangan** | Penerimaan bulan ini, tingkat penagihan, tunggakan per kelas, 10 tunggakan terbesar, ekspor CSV |

### 3.6 PPDB
1. Buat **Gelombang PPDB**: tanggal, kuota, dan biaya.
2. Calon siswa mendaftar di `/ppdb` dan mengunggah dokumen.
3. Di **Pendaftaran PPDB**, periksa dokumen lalu ubah status (diverifikasi, diterima, ditolak). Orang tua mendapat notifikasi WhatsApp.
4. Pendaftar yang diterima → **Daftarkan sebagai siswa**. Data siswa, orang tua, dan akun login dibuat otomatis.

### 3.7 Komunikasi & Website
| Menu | Fungsi |
|---|---|
| **Pengumuman** | Untuk semua, guru, siswa, atau orang tua; bisa disematkan |
| **Pesan** | Kotak pesan dengan guru, siswa, dan orang tua |
| **Broadcast WhatsApp** | Kirim ke semua orang tua, per tingkat, atau per kelas; nomor ganda dikirim sekali |
| **Log WhatsApp** | Status pengiriman dan kirim ulang |
| **Template Notifikasi** | Isi pesan otomatis (absen, tagihan, PPDB) |
| **Berita, Agenda, Galeri** | Konten website sekolah |
| **Layar Lobi** | Buka `/display` di TV lobi: jam, pengumuman, agenda, prestasi, kehadiran hari ini |

### 3.8 Sarana Prasarana
**Perpustakaan** (buku dan peminjaman), **Aset/Inventaris**, **Fasilitas & Peminjaman Ruang**.

---

## 4. Guru

| Menu | Fungsi |
|---|---|
| **Presensi Saya** | Presensi masuk dan pulang dari HP (GPS, dalam radius sekolah) |
| **Jadwal Saya** | Jadwal mengajar mingguan |
| **Sesi Presensi** | Buka sesi, lalu **Tampilkan QR** (siswa memindai) atau **Isi Absensi** manual. Izin yang disetujui terisi otomatis. |
| **Penilaian & Nilai** | Buat penilaian dan isi nilai (bisa Import dari Excel) |
| **Tugas** | Buat tugas (petunjuk, file materi, tenggat); periksa dan nilai jawaban; **Kirim ke buku nilai** |
| **Ujian Online** | Buat ujian pilihan ganda, atur jadwal dan durasi, tambah soal, lihat hasil; **Kirim ke buku nilai** |
| **Pengajuan Izin** | Wali kelas menyetujui atau menolak izin siswa |
| **Pelanggaran** | Catat pelanggaran siswa |
| **Pesan** | Komunikasi dengan orang tua dan siswa |

---

## 5. Siswa

| Menu | Isi |
|---|---|
| **Dashboard** | Jadwal hari ini, kehadiran bulan ini, nilai terbaru, tagihan, pengumuman |
| **Jadwal, Kehadiran, Nilai, Rapor** | Data akademik; rapor PDF dapat diunduh setelah diterbitkan |
| **Tugas** | Lihat tugas, kumpulkan jawaban (teks/file), lihat nilai dan umpan balik |
| **Ujian Online** | Kerjakan ujian saat dibuka. Timer berjalan, jawaban tersimpan otomatis, dan ujian terkumpul otomatis saat waktu habis. |
| **Tagihan & Pembayaran** | Tagihan SPP, bayar online (jika aktif), unduh kwitansi |
| **Perpustakaan & Kegiatan** | Pinjaman buku dan ekstrakurikuler |
| **Pengajuan Izin** | Ajukan izin/sakit dengan lampiran surat |
| **Tabungan** | Saldo dan riwayat transaksi |
| **Kedisiplinan & BK** | Poin pelanggaran, prestasi, catatan BK yang dibagikan |
| **Presensi QR** | Pindai QR dari guru, lalu presensi tercatat (harus login sebagai siswa di kelas tersebut) |

---

## 6. Orang Tua

Akun orang tua terhubung ke anak melalui **email orang tua di data siswa**. Satu akun dapat memantau beberapa anak.

- **Anak Saya**: ringkasan setiap anak.
- Per anak tersedia menu yang sama dengan siswa (jadwal, kehadiran, nilai, rapor, tugas, ujian, tagihan, izin, tabungan, kedisiplinan), dalam mode hanya-baca. Orang tua dapat **mengajukan izin** dan **membayar tagihan**.
- **Pesan**: menghubungi wali kelas atau guru.

---

## 7. Halaman Publik

| URL | Isi |
|---|---|
| `/profile` | Website sekolah: beranda, profil, berita, agenda, prestasi, galeri, guru, kontak |
| `/ppdb` | Pendaftaran siswa baru dan cek status (nomor pendaftaran + tanggal lahir) |
| `/alumni` | Direktori alumni |
| `/display` | Layar TV lobi |
| `/register` | Pendaftaran sekolah baru (SaaS, masa trial) |

---

## 8. Alur Kerja Penting

**Awal tahun ajaran:**
1. Buat tahun ajaran dan semester baru, lalu tandai aktif.
2. Buat kelas baru.
3. Jalankan **Kenaikan Kelas** dari kelas lama ke kelas baru.
4. Jalankan **Kelulusan** untuk kelas akhir.
5. Susun ulang mapel per kelas dan jadwal (Import).

**Bulanan (otomatis):**
- Tagihan SPP dibuat tanggal 1.
- Pengingat tunggakan dikirim setiap Senin.
- Pantau **Laporan Keuangan**.

**Akhir semester:**
1. Pastikan nilai lengkap (termasuk **Kirim ke buku nilai** dari tugas dan ujian online).
2. **Buat Rapor**.
3. Periksa, lalu **Terbitkan**. Siswa dan orang tua dapat mengunduh PDF.

---

## 9. Pertanyaan Umum

**Orang tua tidak melihat anaknya.**
Pastikan email pada data orang tua di menu Siswa sama persis dengan email akun orang tua.

**Import tidak selesai.**
Proses berjalan di latar belakang. Tunggu notifikasi. Jika lama, hubungi admin server untuk memastikan *queue worker* berjalan.

**Guru tidak bisa presensi GPS.**
Izinkan akses lokasi di browser, dan pastikan guru berada dalam radius yang diatur di Profil Sekolah.

**Siswa kehabisan waktu ujian.**
Jawaban yang sudah tersimpan otomatis tetap dinilai. Setiap siswa hanya mendapat satu kali kesempatan.

**Pembayaran online belum muncul.**
Pembayaran tercatat setelah gateway mengirim konfirmasi (webhook). Admin juga dapat memeriksanya di menu Pembayaran.

---

Butuh instalasi, pelatihan, atau kustomisasi? Hubungi **numintek — PT Danum Inovasi Teknologi**: https://numintek.com · WhatsApp +62 852-2009-1770
