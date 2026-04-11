# PR #19 - Unified Login, School Profile Frontend & Alumni/Graduation Management

## Ringkasan

PR ini mengimplementasikan 3 fitur utama yang diminta oleh klien Codester "diginesia":

### 1. Unified Login Page (Halaman Login Terpadu)
- **Semua role user** (Super Admin, School Admin, Operator, Teacher) login di **satu halaman yang sama** (`/school/login`)
- Sistem otomatis mendeteksi role user dan mengarahkan ke panel yang sesuai setelah login
- Route `/login`, `/super-admin/login`, dan `/teacher/login` di-redirect ke halaman login terpadu
- **Menghapus demo credentials** dari halaman login (untuk persiapan upload source code ke Codester)
- **Menambahkan fitur bilingual** pada halaman login dengan language switcher (ID/EN)

### 2. Frontend Web Profil Sekolah
- Halaman profil sekolah publik yang **responsif dan premium** untuk setiap tenant
- Desain profesional dengan color scheme Navy (#1E3A5F) + Gold (#F59E0B)
- Sections: Hero, Tentang Sekolah, Visi & Misi, Guru & Tenaga Pendidik, Fasilitas, Berita/Pengumuman, Kontak, Footer
- Statistik sekolah ditampilkan (jumlah siswa, guru, alumni, fasilitas)
- **Halaman admin settings** di panel School Admin untuk mengelola profil sekolah
- Kolom baru di tabel tenants: vision, mission, description, accreditation, founded_year, website, gallery, social_links

### 3. Fitur Kelulusan & Alumni
- **Alumni Resource** (CRUD lengkap): data alumni, info kelulusan, pendidikan lanjutan, pekerjaan, kontak
- **Graduation Management**: halaman untuk proses kelulusan massal (bulk graduate)
  - Pilih kelas dan tahun kelulusan
  - Centang siswa yang akan diluluskan
  - Otomatis mengubah status siswa menjadi ALUMNI dan membuat profil alumni dengan nomor alumni otomatis (ALM-YYYY-XXXXX)
- Model `AlumniProfile` dengan relasi ke `Student`
- Filter alumni berdasarkan tahun kelulusan dan status verifikasi

## File yang Diubah

### File Baru (14 file)
- `app/Filament/SchoolAdmin/Pages/SchoolProfile.php` - Halaman settings profil sekolah
- `app/Filament/SchoolAdmin/Pages/GraduationManagement.php` - Halaman manajemen kelulusan
- `app/Filament/SchoolAdmin/Resources/AlumniResource.php` - Resource CRUD alumni
- `app/Filament/SchoolAdmin/Resources/AlumniResource/Pages/ListAlumni.php`
- `app/Filament/SchoolAdmin/Resources/AlumniResource/Pages/CreateAlumni.php`
- `app/Filament/SchoolAdmin/Resources/AlumniResource/Pages/EditAlumni.php`
- `app/Http/Controllers/SchoolProfileController.php` - Controller profil publik
- `app/Models/AlumniProfile.php` - Model profil alumni
- `database/migrations/2024_01_02_000001_add_profile_columns_to_tenants_table.php`
- `database/migrations/2024_01_02_000002_create_alumni_profiles_table.php`
- `resources/views/school-profile/index.blade.php` - View profil publik
- `resources/views/filament/school-admin/pages/school-profile.blade.php`
- `resources/views/filament/school-admin/pages/graduation-management.blade.php`

### File yang Dimodifikasi (7 file)
- `app/Filament/Pages/Auth/Login.php` - Override authenticate() untuk redirect berdasarkan role
- `resources/views/filament/login.blade.php` - Hapus demo credentials, tambah bilingual
- `routes/web.php` - Redirect routes + school profile routes
- `app/Models/Tenant.php` - Tambah kolom profil
- `app/Models/Student.php` - Tambah relasi alumniProfile
- `lang/id.json` - ~90 translation keys baru
- `lang/en.json` - ~90 translation keys baru

## Dampak
- **Database**: 2 migration baru (perlu `php artisan migrate`)
- **Login flow**: Semua login URL mengarah ke satu halaman
- **UI**: Halaman login lebih bersih tanpa demo credentials
- **Tenant**: Kolom profil baru untuk personalisasi sekolah
- **Student**: Relasi baru ke alumni profile

## Testing
- ✅ Semua file PHP bebas dari error
- ✅ File JSON (translations) valid
- ✅ Kompatibel dengan arsitektur multi-tenant yang ada
