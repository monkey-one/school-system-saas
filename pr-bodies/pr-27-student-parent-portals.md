# PR #27 — Portal Siswa & Orang Tua Lengkap, Absensi QR Guru, Pesan, Rapor PDF & Data Demo Realistis

## Ringkasan

Menjawab catatan calon client di Codester ("Do you have student and parent login panel testing account?"). Sebelumnya portal siswa dan orang tua **tidak bisa dipakai sama sekali**: tidak ada akun siswa/ortu di seeder, view memanggil route yang tidak ada (`student.spp.pay`, `parent.messages.send`), tabel pesan memakai kolom yang tidak ada (`recipients`), view pembayaran & rapor PDF tidak ada, dan guru tidak punya cara menampilkan QR absensi. PR ini menulis ulang kedua portal secara menyeluruh, menambahkan fitur pendukung di panel guru/admin, dan membuat data demo yang lengkap serta selalu "hari ini".

## Portal Siswa (`/student-portal`)

Dashboard (jadwal hari ini, kehadiran bulan ini, tagihan, nilai terbaru, pengumuman), Jadwal mingguan, Kehadiran per bulan, Nilai per semester per mapel (termasuk remedial), Rapor (unduh PDF), Tagihan & Pembayaran (bayar online via Midtrans, riwayat & kwitansi PDF), Perpustakaan & Ekstrakurikuler, Pengumuman (sesuai target: semua/siswa/kelas tertentu), Pesan ke wali kelas & guru mapel, Akun (data diri + ganti kata sandi).

## Portal Orang Tua (`/parent-portal`)

Beranda semua anak (1 akun → banyak anak, dihubungkan lewat email di data orang tua siswa), halaman yang sama seperti portal siswa per anak, pemilih anak, pesan ke guru anak, pengumuman untuk orang tua.

Kode kedua portal dibagi dalam `PortalController` (abstrak) + `StudentOverview` (query) sehingga tidak ada duplikasi; otorisasi per siswa di setiap aksi (rapor, tagihan, kwitansi, pesan).

## Panel Guru & Admin

- **Tampilkan QR** (JWT 15 menit, dibuat ulang tiap dialog dibuka) + **Isi Absensi** manual per kelas (notifikasi WA ke orang tua saat alfa) — di panel guru dan admin. Aksi lama "Generate QR" hanya membuat string acak yang tidak bisa dipakai.
- **Pesan**: inbox guru (baru) & pusat pesan admin (ditulis ulang) dengan percakapan per thread dan balas. Kolom `->html()` pada isi pesan dihapus (celah XSS begitu orang tua bisa mengirim pesan).
- **Rapor**: tombol "Buat Rapor" per kelas, unduh PDF, terbitkan massal. `RaporService::pdfData()` baru — sebelumnya PDF rapor hampir kosong karena variabel view tidak cocok. Perhitungan nilai akhir kini hanya membagi bobot jenis penilaian yang sudah ada nilainya (sebelumnya rapor tengah semester maksimal ~70 → semua predikat D).

## Data Demo (`DemoSeeder` ditulis ulang)

Semua tanggal relatif terhadap hari seeding (tahun ajaran, semester, tagihan, PPDB, pengumuman), sehingga demo selalu terlihat terkini. Isi: 9 kelas, 12 mapel, 20 guru, 150 siswa (akun login), 300 data orang tua, jadwal tanpa bentrok guru, buku nilai lengkap kelas 7A, absensi 3 minggu + 1 sesi terbuka hari ini (untuk demo QR), absensi guru, SPP + dana kegiatan + beasiswa + pembayaran, rapor terbit kelas 7A, PPDB 2 gelombang aktif, perpustakaan & peminjaman, aset, fasilitas & booking, ekstrakurikuler, 18 alumni + testimoni, percakapan contoh, template WhatsApp, dan 3 sekolah lain untuk dashboard Super Admin.

Akun demo (password `password`): `superadmin@edusaas.id`, `admin@smpn1demo.id`, `operator@smpn1demo.id`, `guru@smpn1demo.id`, `siswa@smpn1demo.id`, `ortu@smpn1demo.id` (2 anak).

## Lainnya

- Migrasi: `messages.recipient_id`, `messages.student_id`, `messages.subject`.
- `SafeHtml::basic()` untuk menampilkan konten rich-text admin di portal tanpa atribut/skrip.
- `public/index.php` kembali standar: prefix sub-folder `/school-system-demo` yang di-hardcode dihapus (penanganan sub-folder dipindah ke konfigurasi nginx, lihat dokumentasi deploy).
- 116 kunci terjemahan baru (ID/EN).
- `PortalTest`: redirect login siswa/ortu, semua halaman portal 200, ortu tidak bisa membuka anak lain, siswa tidak bisa unduh rapor siswa lain, tipe user lain ditolak, pembayaran tanpa gateway, pesan hanya ke guru yang diizinkan, thread privat.

## Dampak

- Route portal lama (`student.spp`, `student.rapor`, `parent.spp`, dll.) diganti nama baru (`student.bills`, `student.report-cards`, `parent.bills`, dll.).
- Deploy: `php artisan migrate --force` (atau `migrate:fresh --seed` untuk demo).

🤖 Generated with [Claude Code](https://claude.com/claude-code)
