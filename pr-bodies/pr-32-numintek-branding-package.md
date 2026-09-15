## Ringkasan

PR ini menyiapkan live demo Codester dan paket rilis: branding numintek khusus demo, akun demo di halaman login, dokumentasi baru, dan konfigurasi yang lebih aman untuk pembeli.

### Live demo (hanya aktif jika `DEMO_MODE=true`)
- **CTA numintek** berupa kartu kecil melayang di pojok kanan bawah. Kartu berisi tautan ke https://numintek.com dan WhatsApp, bisa ditutup, dan muncul di panel Filament, portal siswa/orang tua, website sekolah, dan landing page.
- **Akun demo di halaman login**: 6 peran (Super Admin, Admin Sekolah, Operator, Guru, Siswa, Orang Tua). Klik salah satu akun untuk mengisi form login.
- **Landing page**: bagian *Coba Demo* menampilkan keenam akun, tautan ke website sekolah, PPDB, layar lobi, dan alumni, serta ajakan menghubungi numintek.
  - Tombol *Lihat Demo* dan bagian demo **tidak tampil** di instalasi pembeli. Sebelumnya kredensial demo selalu tampil di landing page, termasuk email guru yang salah dan URL login lama.
- Daftar akun demo sekarang disimpan di `config/demo.php`.

### Hak cipta & kredit
- Footer login, panel, portal, landing, dan halaman registrasi kini menampilkan `© tahun, nama aplikasi (APP_NAME)`, ditambah *Dikembangkan oleh PT Danum Inovasi Teknologi*.
- Kredit dapat diubah atau disembunyikan lewat `APP_AUTHOR_*` di `.env`.

### Reset demo lebih bersih
- `edusaas:demo-reset` kini juga menghapus file privat unggahan pengunjung: dokumen PPDB, surat izin, tugas, jawaban, dokumen siswa, dan file import/export.
- Perintah ini juga menjalankan `queue:clear`, `queue:restart`, dan `cache:clear`.

### Konfigurasi & dokumentasi
- **`.env.example` untuk pembeli**
  - `FILESYSTEM_DISK=local` (file privat tidak publik) dan `QUEUE_CONNECTION=database` / `CACHE_STORE=database` (tidak wajib Redis).
  - Variabel baru: `APP_BASE_DOMAINS`, `APP_DEFAULT_TENANT`, `DEMO_MODE=false`, dan `APP_AUTHOR_*`.
  - Catatan produksi: `APP_DEBUG`, `SESSION_SECURE_COOKIE`, dan penggantian password super admin.
- **README** ditulis ulang: fitur lengkap, instalasi singkat, akun demo, tautan dokumentasi, dan kontak numintek. Boilerplate Laravel dan URL lama dihapus.
- **Dokumen baru**
  - `docs/USER_GUIDE.md`: panduan per peran dan alur kerja penting.
  - `docs/DEVELOPER_GUIDE.md`: arsitektur, tenancy, struktur folder, instalasi, Nginx (termasuk sub-folder), Supervisor, cron, `.env`, database, keamanan, pengembangan, update, dan pemecahan masalah.
- **Dokumen yang dihapus** karena usang dan tergantikan panduan baru: `docs/INSTALLATION.md`, `docs/USER_MANUAL.md`, dan `docs/FEATURES.md`. Dokumen lama masih menyebut Redis wajib, Horizon, dan URL panel yang sudah tidak ada.

### Pengujian
- `tests/Feature/DemoBrandingTest.php` mencakup:
  - tanpa `DEMO_MODE`, kredensial dan CTA tidak tampil di login maupun landing page;
  - dengan `DEMO_MODE`, keduanya tampil;
  - kredit pengembang dapat disembunyikan.
- Seluruh test suite dan crawler halaman dijalankan di VPS.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
