## Ringkasan

PR ini menambah modul **E-Learning** (tugas online) dan **Ujian Online / CBT** yang terhubung ke buku nilai dan rapor.

### Panel Guru
- **Tugas**
  - Guru membuat tugas per kelas dan mapel yang ia ajar, dengan petunjuk rich text, lampiran materi (disk privat), tenggat, nilai maksimal, dan opsi menerima pengumpulan terlambat.
  - Tab *Pengumpulan*: melihat jawaban teks dan file siswa, lalu memberi nilai dan umpan balik.
  - Tombol **Kirim ke buku nilai** membuat penilaian "Tugas" dan menyalin nilai (dikonversi ke skala 100). Menjalankan ulang akan memperbarui nilai.
- **Ujian Online**
  - Pengaturan: jadwal buka dan tutup, durasi, acak soal, tampilkan nilai, dan status terbit.
  - Soal pilihan ganda A–E dengan kunci jawaban dan bobot poin.
  - Tab *Hasil* berisi daftar peserta, status, jawaban benar, dan nilai.
  - Tombol **Kirim ke buku nilai** membuat penilaian "Ulangan Harian".

### Portal Siswa
- **Tugas**
  - Tugas dikelompokkan menjadi *Belum dikerjakan*, *Sudah dikumpulkan*, dan *Sudah dinilai*.
  - Tugas yang lewat tenggat ditandai.
  - Siswa mengumpulkan teks dan/atau file, dan boleh kumpul ulang sampai dinilai.
  - Pengumpulan yang lewat tenggat otomatis bertanda *terlambat*.
- **Ujian Online**
  - Timer hitung mundur, jawaban tersimpan otomatis setiap 20 detik, dan ujian terkumpul otomatis saat waktu habis.
  - Satu kali percobaan per siswa, lalu halaman hasil ditampilkan.

### Portal Orang Tua
- Melihat tugas anak (status, nilai, umpan balik) dan hasil ujian online. Tampilan hanya baca.

### Keamanan
- Tugas dan ujian hanya bisa dibuka siswa **di kelas yang bersangkutan** dan hanya yang sudah terbit. Kelas lain mendapat 404.
- Kunci jawaban tidak pernah dikirim ke browser. Penilaian dilakukan di server.
- Batas waktu dicek di server:
  - Jawaban yang dikirim setelah tenggat (lewat masa tenggang 60 detik) tidak dipakai. Yang dinilai hanya jawaban yang tersimpan sebelum tenggat.
  - Jawaban hanya diterima untuk soal milik ujian itu dengan huruf pilihan yang valid.
  - Pengumpulan dikunci dengan `lockForUpdate` agar tidak terjadi pengumpulan ganda.
- File materi dan jawaban disimpan di disk **privat** dan dilayani lewat controller dengan cek akses (staf, guru pengampu, siswa, orang tua).
- Petunjuk tugas (rich text) disanitasi sebelum ditampilkan untuk mencegah XSS.
- Pada demo publik, siswa hanya bisa mengumpulkan jawaban teks. Unggah file ditolak.
- Guru hanya melihat dan mengubah tugas atau ujian miliknya. `teacher_id` selalu diisi di server.
- Endpoint autosave dibatasi 60 request per menit, dan pengumpulan tugas memakai rate limiter `public-forms`.

### Data demo (kelas 7A)
- 5 tugas: 1 sudah dinilai dan masuk buku nilai, 1 sudah dikumpulkan, 3 masih terbuka.
- 3 ujian Matematika berisi 10 soal:
  - 1 sudah selesai, dengan nilai seluruh siswa yang sudah masuk buku nilai;
  - 1 **sedang dibuka dan bisa langsung dicoba** dengan akun `siswa@smpn1demo.id`;
  - 1 terjadwal.

### Pengujian
- `tests/Feature/ElearningTest.php` mencakup:
  - pengumpulan tugas dan sanitasi petunjuk;
  - tugas yang sudah dinilai tidak bisa diganti;
  - unggah file diblokir di demo;
  - akses kelas lain ditolak dan akses file dibatasi;
  - alur ujian: autosave, penilaian, dan kunci jawaban tidak bocor;
  - jawaban yang terlambat diabaikan;
  - ujian yang tertutup tidak bisa dimulai;
  - orang tua hanya bisa melihat anaknya sendiri;
  - sinkronisasi ke buku nilai tidak menggandakan nilai.
- Seluruh test suite dan crawler halaman dijalankan di VPS.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
