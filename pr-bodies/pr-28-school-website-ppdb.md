# PR #28 — Website Sekolah (CMS) & PPDB Online yang Benar-Benar Berfungsi

## Ringkasan

Menjawab catatan calon client ("frontend web profilnya" dan permintaan fitur mirip schoolpro.id). Sebelumnya profil sekolah hanya satu halaman statis, dan halaman PPDB publik **tidak berfungsi**: form pendaftaran & cek status memanggil endpoint `/api/ppdb/*` yang tidak pernah ada, view surat penerimaan tidak ada, dan dokumen tidak pernah tersimpan. PR ini membangun website sekolah lengkap dengan CMS di panel admin serta alur PPDB online dari pendaftaran hingga siswa terdaftar.

## Website Sekolah (publik, per tenant)

| Halaman | URL |
|---|---|
| Beranda (hero, statistik, sambutan kepala sekolah, berita, agenda, prestasi, galeri, guru, banner PPDB) | `/profile` |
| Profil (tentang, sejarah, visi-misi, identitas, fasilitas) | `/profile/about` |
| Berita & artikel (kategori, pencarian, detail + share WhatsApp/Facebook/X, berita terkait) | `/profile/news` |
| Agenda / kalender kegiatan | `/profile/agenda` |
| Prestasi (filter tingkat) | `/profile/achievements` |
| Galeri foto & video YouTube (lightbox) | `/profile/gallery` |
| Direktori guru & tenaga kependidikan (pencarian) | `/profile/teachers` |
| Kontak (WhatsApp, Google Maps, jam layanan) | `/profile/contact` |
| Direktori alumni + testimoni | `/alumni` |
| Sitemap XML | `/sitemap.xml` |

SEO: meta description, Open Graph, canonical, JSON-LD `School`. Layout responsif satu untuk semua halaman publik (termasuk PPDB & alumni).

## CMS di Panel Admin Sekolah (grup menu baru "Website")

- **Berita & Artikel** (rich text, cover, unggulan, jadwal terbit, slug otomatis, jumlah dilihat)
- **Agenda**, **Prestasi** (tingkat & kategori, tautan ke siswa), **Galeri** (album + foto/video, urutan drag & drop)
- **Profil Sekolah** diperluas: logo, judul & gambar hero, sambutan & foto kepala sekolah, NIP kepala sekolah, sejarah, WhatsApp, jam layanan, tautan share semua halaman publik.

Konten rich text dirender melalui `SafeHtml::basic()` (tanpa skrip/atribut).

## PPDB Online

1. Daftar gelombang (buka / segera dibuka, kuota, persyaratan, jumlah pendaftar) + alur 4 langkah.
2. Form pendaftaran server-side dengan unggah dokumen (akta, KK, pas foto, ijazah/SKL) — divalidasi berdasarkan isi file (mimes), maks 2 MB, disimpan di **disk privat**.
3. Halaman sukses + **bukti pendaftaran PDF** (URL bertanda tangan).
4. Cek status memakai **nomor pendaftaran + tanggal lahir** (mencegah enumerasi data pendaftar), catatan dari sekolah, unduh **surat penerimaan PDF** (signed).
5. Admin: badge jumlah menunggu, lihat dokumen (hanya admin/operator), terima/cadangkan/tolak dengan catatan → **notifikasi WhatsApp** otomatis ke orang tua, aksi massal, dan **"Daftarkan sebagai siswa"**: membuat data siswa (NIS otomatis), data wali, akun portal siswa & orang tua (password acak ditampilkan sekali).

## Perbaikan & Keamanan

- **Signed URL di sub-folder**: aplikasi di `/edusaas` membuat URL dengan prefix tetapi memvalidasi tanpa prefix, sehingga semua signed URL (termasuk halaman pembayaran registrasi) selalu invalid. Solusi: dukungan header `X-Forwarded-Prefix` (nginx mengirim prefix; routing tetap bersih). Dokumentasi deploy sub-folder diperbarui.
- Job notifikasi (absen alfa, status PPDB) kini berjalan dalam konteks tenant sekolah terkait — sebelumnya worker antrean bisa memakai template WhatsApp milik sekolah lain.
- Persyaratan gelombang PPDB di seeder disesuaikan dengan field KeyValue admin.

## Data Demo

Konten website lengkap: pengaturan beranda, 8 berita, 8 agenda, 8 prestasi, 4 album (foto ilustrasi SVG yang dibuat seeder + video YouTube).

## Test

`WebsiteTest` (semua halaman publik 200, sitemap, sanitasi konten & draf tersembunyi, pendaftaran PPDB dengan dokumen + cek status, upload `.php` ditolak, surat penerimaan wajib signed, dokumen hanya untuk staf) dan `PPDBTest` diperbarui ke alur baru.

## Dampak

- Route `profile.index` diganti `website.home` (URL `/profile` tetap).
- Migrasi baru: `posts`, `school_events`, `achievements`, `gallery_albums`, `gallery_items`.
- Nginx sub-folder perlu `fastcgi_param HTTP_X_FORWARDED_PREFIX /edusaas;`.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
