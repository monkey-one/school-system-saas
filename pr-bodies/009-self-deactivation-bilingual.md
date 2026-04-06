# PR #9 - Fix Self-Deactivation & Panel Bilingual

## Ringkasan

Memperbaiki bug kritis dimana admin bisa menonaktifkan akun sendiri (menyebabkan 403 Forbidden), serta menambahkan dukungan bilingual (Indonesia/English) penuh ke semua panel admin.

## Perubahan Utama

### 🛡️ Perlindungan Self-Deactivation
- Toggle `is_active` di tabel user sekarang **disabled** untuk user yang sedang login
- Toggle `is_active` di form edit juga **disabled** untuk akun sendiri
- Tombol **Delete** disembunyikan untuk akun sendiri
- Mencegah admin mengunci diri sendiri dari sistem

### 🌐 Dukungan Bilingual (ID/EN)
- **35 resource files** dikonversi dari label hardcoded Indonesia ke `__()` translation
- **2 RelationManager** (Parents, Documents) juga dikonversi
- Semua `protected static ?string` property diganti dengan method override (`getNavigationGroup()`, `getNavigationLabel()`, `getModelLabel()`, `getPluralModelLabel()`)
- Semua `->label()`, `->description()`, `->placeholder()`, `Section::make()`, dll. menggunakan `__()` dengan key bahasa Inggris

### 🔤 Language Switcher
- Tombol toggle **ID/EN** ditambahkan di top bar semua panel (SuperAdmin, SchoolAdmin, Teacher)
- Menggunakan render hook `GLOBAL_SEARCH_BEFORE`
- Memanfaatkan route `/locale/{locale}` yang sudah ada

### 📝 Translation Keys
- **265+ key baru** ditambahkan ke `lang/en.json` dan `lang/id.json`
- Total sekarang **599 key** untuk masing-masing bahasa
- Mencakup: navigasi, label form, label tabel, header section, action button, filter, modal, dll.

### 🏗️ Navigation Groups
- SuperAdmin panel sekarang memiliki navigation groups terstruktur:
  - **User Management** / Manajemen User
  - **Tenant Management** / Manajemen Tenant
- SchoolAdmin panel sudah memiliki groups sebelumnya, sekarang resource-nya cocok dengan key terjemahan

## File yang Diubah
- 63 file (35 resource + 20 page + 2 relation manager + 2 provider + 2 lang + 1 view baru + 1 provider)
- `+1785 -887` baris kode

## Dampak
- Admin panel sekarang bisa diganti bahasa secara langsung tanpa reload (ID ↔ EN)
- Tidak ada resiko admin mengunci diri sendiri lagi
- Backward compatible - default locale tetap `id` (Indonesia)
