# PR #22 - Fix: Satu Halaman Login di /edusaas-admin/login

## Ringkasan

Mengubah sistem menjadi benar-benar **satu halaman login** untuk semua role user di path `/edusaas-admin/login`.

## Perubahan

### 1. Path Panel School Admin
- Diubah dari `/school` menjadi `/edusaas-admin`
- Ini adalah satu-satunya panel yang memiliki halaman login

### 2. Hapus Login dari Panel Lain
- Super Admin panel (`/super-admin`) — login page dihapus
- Teacher panel (`/teacher`) — login page dihapus
- Kedua panel ini tidak lagi memiliki halaman login sendiri

### 3. Redirect Routes
- `/login` → redirect ke `/edusaas-admin/login`
- `/super-admin/login` → redirect ke `/edusaas-admin/login` (named route: `filament.super-admin.auth.login`)
- `/teacher/login` → redirect ke `/edusaas-admin/login` (named route: `filament.teacher.auth.login`)
- Named routes diperlukan agar auth middleware Filament bisa redirect user yang belum login

### 4. Custom LogoutResponse
- Dibuat `App\Http\Responses\LogoutResponse` yang selalu redirect ke `/edusaas-admin/login`
- Di-bind di `AppServiceProvider` menggantikan Filament default
- Mencegah error 500 saat logout dari panel yang tidak punya login page

### 5. Update Referensi
- Landing page: semua link `/school/login` → `/edusaas-admin/login`
- Registration success pages: `/school` → `/edusaas-admin`

## File yang Diubah
- `app/Providers/Filament/SchoolAdminPanelProvider.php` — path `/school` → `/edusaas-admin`
- `app/Providers/Filament/SuperAdminPanelProvider.php` — hapus `->login()` dan `->passwordReset()`
- `app/Providers/Filament/TeacherPanelProvider.php` — hapus `->login()` dan `->passwordReset()`
- `app/Filament/Pages/Auth/Login.php` — redirect default ke `/edusaas-admin`
- `app/Http/Responses/LogoutResponse.php` — **BARU** custom logout response
- `app/Providers/AppServiceProvider.php` — bind LogoutResponse
- `routes/web.php` — redirect routes dengan named routes
- `resources/views/landing/index.blade.php` — update link login
- `resources/views/registration/success.blade.php` — update link
- `resources/views/registration/trial-success.blade.php` — update link

## Dampak
- **BREAKING**: URL panel school admin berubah dari `/school` ke `/edusaas-admin`
- Semua bookmark/link lama ke `/school/...` akan 404 → user perlu akses via `/edusaas-admin/...`
- Logout dari semua panel sekarang redirect ke `/edusaas-admin/login`
- Tidak ada perubahan database
