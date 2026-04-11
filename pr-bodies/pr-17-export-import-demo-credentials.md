# PR #17: Export/Import untuk Semua Resource + Demo Credentials

## Ringkasan

PR ini menambahkan fitur **export/import data** untuk siswa, guru, jadwal mengajar, mata pelajaran, dan nilai menggunakan sistem built-in Filament 3. Juga memperbaiki masalah akses demo untuk School Admin dan Teacher panel dengan menampilkan **credentials demo** langsung di halaman login dan landing page.

## Perubahan yang Dilakukan

### 1. Fitur Export/Import (Request dari diginesia)
- **Migrasi database**: Menambahkan tabel `exports`, `imports`, dan `failed_import_rows` yang diperlukan Filament export/import system
- **Exporter classes**: StudentExporter, TeacherExporter, SubjectExporter, TeachingScheduleExporter, StudentGradeExporter
- **Importer classes**: StudentImporter, TeacherImporter, SubjectImporter, TeachingScheduleImporter, StudentGradeImporter
- **Fix StudentResource**: Export/import sebelumnya tidak berfungsi karena belum ada referensi ke Exporter/Importer class
- **TeacherResource**: Ditambah tombol Ekspor dan Impor Excel
- **SubjectResource**: Ditambah tombol Ekspor dan Impor Excel
- **TeachingScheduleResource**: Ditambah tombol Ekspor dan Impor Excel
- **AssessmentResource** (SchoolAdmin): Ditambah tombol Ekspor Nilai dan Impor Nilai
- **StudentGradeResource** (Teacher panel): Ditambah tombol Ekspor dan Impor Nilai

### 2. Fix Demo Access (Request dari bobwoods)
- **Login page**: Menampilkan demo credentials (email + password) di setiap halaman login panel, dengan link navigasi ke panel lain. Hanya muncul ketika `APP_DEFAULT_TENANT=demo`
- **Landing page**: Menambahkan section "Demo Access" dengan 3 card untuk Super Admin, School Admin, dan Teacher — masing-masing dengan credentials dan tombol login langsung

### 3. Bilingual Support
- Menambahkan terjemahan bahasa Indonesia untuk semua string baru di `lang/id.json`

## Dampak
- Pengguna demo bisa langsung melihat credentials dan login sebagai School Admin atau Teacher
- School admin bisa export/import data siswa, guru, mapel, jadwal, dan nilai dalam format Excel/CSV
- Teacher bisa export/import nilai siswa langsung dari panel mereka
- Tidak ada breaking change pada fitur existing

## Resource yang Sudah Ada Export/Import

| Resource | Ekspor | Impor |
|----------|--------|-------|
| Siswa (StudentResource) | ✅ | ✅ |
| Guru (TeacherResource) | ✅ | ✅ |
| Mata Pelajaran (SubjectResource) | ✅ | ✅ |
| Jadwal Mengajar (TeachingScheduleResource) | ✅ | ✅ |
| Nilai (StudentGradeResource / AssessmentResource) | ✅ | ✅ |

## Catatan Deployment
- Perlu jalankan `php artisan migrate` untuk membuat tabel exports, imports, dan failed_import_rows
- Perlu `php artisan config:cache` dan `php artisan view:cache` untuk update cache
