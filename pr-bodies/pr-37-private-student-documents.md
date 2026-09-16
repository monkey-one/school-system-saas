## Ringkasan

Dokumen pribadi siswa dan bukti pembayaran sebelumnya diunggah ke disk **publik**, sehingga berkas seperti Kartu Keluarga, akta kelahiran, dan ijazah dapat diunduh siapa saja yang menebak URL-nya (`/storage/students/documents/...`). PR ini memindahkannya ke disk privat.

### Masalah
`FILESYSTEM_DISK`/`FILAMENT_FILESYSTEM_DISK` di server memakai disk `public`. Tiga kolom unggahan berikut tidak menyebut disk secara eksplisit, sehingga ikut tersimpan publik:

| Kolom | Folder | Isi |
|---|---|---|
| `StudentDocument.file_path` | `students/documents` | Akta lahir, KK, ijazah, rapor, SKHUN |
| `Payment.receipt_path` | `payments/receipts` | Bukti transfer pembayaran |
| `Announcement.attachments` | `announcements/attachments` | Lampiran pengumuman sekolah |

Modul lain yang serupa sudah benar sejak awal: surat izin dan berkas e-learning memakai `->disk('local')->visibility('private')`, PPDB memakai `store(..., 'local')`.

### Perbaikan
- Ketiga kolom di atas kini memakai `->disk('local')->visibility('private')`.
- **`StudentDocumentController`** baru melayani unduhan dengan pengecekan hak akses:
  - staf sekolah (admin/operator) boleh membuka dokumen sekolahnya;
  - siswa hanya dokumen miliknya sendiri;
  - orang tua hanya dokumen anaknya;
  - guru dan orang tua lain ditolak (403), berkas hilang menghasilkan 404.
- Tabel **Dokumen** pada data siswa mendapat tombol **Unduh**, karena berkas privat tidak lagi bisa dibuka lewat URL langsung.
- Rute `students.documents.attachment` diletakkan bersama rute berkas privat lainnya (login + tenant wajib).

### Catatan
Berkas yang terlanjur tersimpan di disk publik tetap berada di sana. Pada live demo, data direset berkala sehingga hilang dengan sendirinya. Untuk instalasi yang sudah berjalan, pindahkan folder `storage/app/public/students/documents` dan `storage/app/public/payments/receipts` ke `storage/app/private/` setelah update.

### Pengujian
`tests/Feature/PrivateFilesTest.php`:
- staf, siswa pemilik, dan orang tuanya dapat mengunduh;
- orang tua lain, guru, dan tamu ditolak;
- berkas yang tidak ada menghasilkan 404;
- ketiga kolom unggahan dipastikan tetap memakai disk privat (mencegah regresi).

🤖 Generated with [Claude Code](https://claude.com/claude-code)
