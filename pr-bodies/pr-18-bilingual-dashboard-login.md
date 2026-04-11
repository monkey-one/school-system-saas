# PR #18 - Bilingual Dashboard, Login & Footer

## Ringkasan

Memperbaiki dukungan bilingual (Indonesia/Inggris) pada halaman dashboard School Admin, halaman login, dan footer. Sebelumnya, teks pada widget dashboard dan beberapa elemen login/footer masih hardcode dalam satu bahasa, sehingga tidak berubah saat pengguna mengganti bahasa.

## Perubahan

### Widget Dashboard (5 file)
- **StatsOverview**: Label dan deskripsi 4 kartu statistik (Total Siswa Aktif, Total Guru Aktif, Pendapatan SPP, Kehadiran) diganti menggunakan `__()`
- **SppRevenueChart**: Heading dan label dataset diganti dari static property ke method `getHeading()` + `__()`
- **AttendanceChart**: Heading dan 4 label dataset (Hadir, Sakit, Izin, Alfa) diganti menggunakan `__()`
- **RecentActivityWidget**: Heading dan 4 label kolom tabel diganti menggunakan `__()`
- **LateSppWidget**: Heading dan 7 label kolom tabel diganti menggunakan `__()`

### Login & Footer
- **login.blade.php**: Teks "Demo Credentials:" dan link panel (Super Admin, School Admin, Teacher) dibungkus `__()`
- **footer.blade.php**: Teks "Sistem Manajemen Sekolah" diganti menggunakan `__()`

### File Terjemahan
- **lang/id.json**: Ditambahkan 21 key terjemahan baru untuk semua string dashboard
- **lang/en.json**: Ditambahkan 21 key terjemahan baru (English)

## Catatan Teknis
- Static property `$heading` pada ChartWidget/TableWidget tidak bisa menggunakan `__()` karena dipanggil saat compile-time. Solusi: override method `getHeading()` yang dipanggil saat runtime.
- Key terjemahan menggunakan bahasa Inggris sebagai default (best practice Laravel), dengan terjemahan Indonesia di `id.json`.

## Dampak
- Semua teks pada dashboard School Admin sekarang mengikuti bahasa yang dipilih pengguna
- Halaman login menampilkan teks sesuai locale
- Footer menampilkan teks sesuai locale
- Tidak ada breaking changes
