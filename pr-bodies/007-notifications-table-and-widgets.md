# Perbaikan Tabel Notifications dan Widget

## Ringkasan
PR ini memperbaiki error 500 yang terjadi di semua panel (super admin, sekolah, guru) setelah login. Penyebab utama adalah tabel `notifications` yang tidak ada di database, sedangkan ketiga panel menggunakan `->databaseNotifications()` dari Filament.

## Perbaikan

### Kritis (Error 500)
- **Tabel `notifications`**: Menambahkan migration untuk tabel notifications yang dibutuhkan oleh Filament DatabaseNotifications. Tanpa tabel ini, semua dashboard dan menu di ketiga panel menghasilkan error 500

### Bug Data
- **MRR Super Admin selalu Rp 0**: `SuperAdminStatsOverview` menggunakan `$plan->price` yang tidak ada di tabel plans. Diperbaiki menjadi `$plan->price_monthly` sesuai kolom yang ada di migration

### Deprecated API
- **LateSppWidget**: Mengganti `BadgeColumn::make()->colors([...])` (API Filament 2) menjadi `TextColumn::make()->badge()->color(...)` (API Filament 3)

## File yang Diubah
- `database/migrations/2024_01_01_000047_create_notifications_table.php` (baru)
- `app/Filament/SuperAdmin/Widgets/SuperAdminStatsOverview.php`
- `app/Filament/SchoolAdmin/Widgets/LateSppWidget.php`
