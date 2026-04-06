# PR #16: Tambah Language Switcher Bilingual di Halaman Registrasi

## Ringkasan
Menambahkan fitur pemilih bahasa (language switcher) di semua halaman registrasi agar pengguna bisa beralih antara Bahasa Indonesia dan English. Sebelumnya halaman registrasi hanya menampilkan teks dalam satu bahasa tanpa opsi untuk mengganti bahasa.

## Perubahan

### Komponen Baru
- **`language-switcher-public.blade.php`**: Komponen dropdown pemilih bahasa yang reusable untuk halaman publik, menampilkan bendera 🇮🇩 Indonesia dan 🇬🇧 English dengan highlight pada bahasa yang aktif.

### View yang Dimodifikasi
- **`registration/index.blade.php`**: Menambahkan language switcher di header (sebelah kanan logo)
- **`registration/payment.blade.php`**: Menambahkan language switcher + Alpine.js CDN
- **`registration/success.blade.php`**: Menambahkan language switcher + Alpine.js CDN
- **`registration/trial-success.blade.php`**: Menambahkan language switcher + Alpine.js CDN

### Terjemahan
- Menambahkan 7 key terjemahan fitur paket yang sebelumnya belum ada di kedua file bahasa:
  - `Spp` → SPP / Tuition Fee
  - `Ppdb` → PPDB / Online Admission
  - `Reports` → Laporan / Reports
  - `Whatsapp` → WhatsApp
  - `Api` → API
  - `Custom domain` → Domain kustom / Custom Domain
  - `Priority support` → Dukungan prioritas / Priority Support

## Dampak
- Semua halaman registrasi sekarang mendukung bilingual (ID/EN)
- Fitur paket langganan yang ditampilkan di halaman registrasi sekarang diterjemahkan dengan benar
- Tidak ada perubahan pada logic backend atau database

## Testing
- Buka halaman registrasi → pastikan ada dropdown bahasa di header
- Klik English → semua teks berubah ke English
- Klik Indonesia → semua teks berubah ke Indonesia
- Cek halaman payment, success, dan trial-success → language switcher berfungsi
