# PR #25: Fix Error 500 pada Halaman Absensi Guru & Ekstrakurikuler

## Ringkasan

Memperbaiki error 500 yang terjadi saat mengakses halaman **Absensi Guru** (`/edusaas-admin/teacher-attendances`) dan **Ekstrakurikuler** (`/edusaas-admin/extracurriculars`).

## Penyebab

Tabel `teachers` menggunakan kolom `full_name`, tetapi `TeacherAttendanceResource` dan `ExtracurricularResource` mereferensikan kolom `name` yang tidak ada.

Error SQL: `Column not found: 1054 Unknown column 'teachers.name'`

## Perubahan

- **`TeacherAttendanceResource.php`** — 3 lokasi:
  - Form select: `relationship('teacher', 'name')` → `relationship('teacher', 'full_name')`
  - Table column: `teacher.name` → `teacher.full_name`
  - Filter: `relationship('teacher', 'name')` → `relationship('teacher', 'full_name')`
- **`ExtracurricularResource.php`** — 2 lokasi:
  - Form select: `relationship('teacher', 'name')` → `relationship('teacher', 'full_name')`
  - Table column: `teacher.name` → `teacher.full_name`

## Dampak
- Fix langsung, tidak ada migrasi, tidak ada side effect
