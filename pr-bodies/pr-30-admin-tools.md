## Ringkasan

PR ini menambah alat kerja harian untuk admin, bendahara, dan guru. PR ini juga memperbaiki angka dashboard yang sebelumnya menyesatkan.

### Fitur baru
- **Presensi GPS guru** (panel Guru → *Presensi Saya*)
  - Guru melakukan presensi masuk dan pulang dari HP.
  - Lokasi dibandingkan dengan koordinat sekolah dan harus berada dalam radius yang diatur.
  - Status *terlambat* terisi otomatis sesuai jam mulai dan toleransi.
  - Metode `gps` tampil di rekap Presensi Guru.
  - Pengaturan ada di *Profil Sekolah → Presensi guru*: latitude, longitude, radius, jam mulai, dan toleransi.
  - Pengecekan lokasi dilewati pada demo publik.
- **Kenaikan Kelas massal** (*Kenaikan Kelas*): pilih kelas asal dan kelas tujuan, centang siswa, lalu naikkan sekaligus. Siswa yang tidak dicentang tetap di kelasnya.
- **Broadcast WhatsApp**
  - Penerima: semua orang tua, orang tua satu tingkat, atau orang tua satu kelas.
  - Placeholder `{name}`, `{student_name}`, dan `{school}` tersedia.
  - Nomor ganda dikirim sekali saja.
  - Pesan dikirim lewat antrean.
  - Dinonaktifkan pada demo publik.
- **Log WhatsApp**: riwayat pengiriman dengan status dan pesan error, plus tombol kirim ulang.
- **Laporan Keuangan**
  - Menampilkan tagihan bulan ini, penerimaan, tingkat penagihan, penerimaan per metode, tunggakan per kelas, dan 10 tunggakan terbesar.
  - Tersedia ekspor CSV.
- **Layar Lobi** (`/display`): tampilan TV untuk lobi sekolah.
  - Isi: jam, pengumuman, agenda, prestasi, berita, persentase kehadiran hari ini, dan teks berjalan.
  - Hanya data publik yang ditampilkan.
- **Widget Tugas Tertunda** di dashboard admin: PPDB menunggu verifikasi, pengajuan izin, tagihan jatuh tempo, dan pesan belum dibaca.

### Perbaikan bug
- Dashboard: pendapatan sekarang hanya menghitung pembayaran yang sudah lunas/terverifikasi, dan kehadiran ikut menghitung status *terlambat*.
- `SendWhatsAppNotification` membawa `tenantId` dan memulihkan tenant sebelumnya setelah job selesai. Sebelumnya log bisa tersimpan tanpa sekolah saat dijalankan worker.

### Data demo
- Koordinat sekolah dan pengaturan presensi terisi.
- Contoh log WhatsApp (terkirim dan gagal) tersedia.

### Pengujian
- `tests/Feature/AdminToolsTest.php` mencakup:
  - presensi GPS (dalam dan luar radius);
  - kenaikan kelas hanya untuk siswa terpilih;
  - broadcast satu pesan per nomor dan diblokir di demo;
  - laporan keuangan hanya menghitung pembayaran lunas, plus ekspor CSV;
  - pendapatan dashboard mengabaikan pembayaran gateway yang masih pending;
  - layar lobi bersifat publik.
- Seluruh test suite dan crawler halaman dijalankan di VPS.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
