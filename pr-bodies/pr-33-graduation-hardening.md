## Ringkasan

PR ini memperkuat fitur **Kelulusan → Alumni** yang diminta calon pembeli, sekaligus menambah pengujian otomatis.

### Perbaikan
- **Dalam satu transaksi database**: jika ada error di tengah proses, tidak ada siswa yang setengah diluluskan (status sudah alumni tetapi profil alumni belum dibuat).
- **Tidak menggandakan data alumni**: menjalankan kelulusan ulang untuk siswa yang sudah lulus tidak lagi membuat profil alumni ganda.
- **Validasi server** untuk tahun lulus (2000 sampai tahun depan) dan daftar siswa. Sebelumnya nilai langsung dipakai tanpa validasi.
- Siswa diambil sekaligus dengan `lockForUpdate` (dibatasi tenant aktif), bukan satu per satu, sehingga lebih cepat untuk kelas besar dan aman dari klik ganda.

### Pengujian
- `tests/Feature/GraduationTest.php` mencakup:
  - siswa terpilih menjadi alumni, sedangkan siswa lain tetap aktif;
  - menjalankan ulang tidak menggandakan data;
  - direktori alumni dapat dibuka;
  - tahun lulus yang tidak valid ditolak.
- Seluruh test suite dan crawler halaman dijalankan di VPS.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
