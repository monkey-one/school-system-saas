# Panduan Kustomisasi (White-Label) EduSaaS

Panduan ini menjelaskan cara melakukan kustomisasi tampilan dan branding untuk setiap sekolah (tenant) di platform EduSaaS.

---

## 1. Kustomisasi Warna

### Via Panel Admin Sekolah

Setiap sekolah dapat mengubah warna tema melalui **Pengaturan Sekolah**:

1. Login sebagai Admin Sekolah
2. Buka menu **Pengaturan** → **Tampilan**
3. Ubah warna primer dan sekunder
4. Klik **Simpan**

Warna disimpan di kolom `settings` pada tabel `tenants`:

```json
{
    "color_primary": "#1e40af",
    "color_secondary": "#f59e0b"
}
```

### Via Kode (Global)

Edit file `tailwind.config.js` untuk mengubah warna default:

```js
module.exports = {
    theme: {
        extend: {
            colors: {
                primary: {
                    // Sesuaikan warna di sini
                    50: '#eff6ff',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                },
            },
        },
    },
};
```

Untuk Filament, sesuaikan di provider:

```php
// app/Providers/Filament/SchoolAdminPanelProvider.php
->colors([
    'primary' => Color::Blue,
])
```

---

## 2. Logo Sekolah

### Upload Logo

1. Login sebagai Admin Sekolah
2. Buka **Pengaturan** → **Profil Sekolah**
3. Upload logo di field **Logo**
4. Logo akan tampil di header panel dan dokumen cetak

### Format yang Didukung

- PNG, JPG, SVG
- Ukuran rekomendasi: 200x200px (persegi) atau 400x100px (landscape)
- Maksimal 2MB

### Logo di Rapor dan Dokumen

Logo otomatis digunakan di:
- Header rapor siswa
- Surat keterangan PPDB
- Kop surat pengumuman
- Invoice/kwitansi pembayaran

---

## 3. Nama & Identitas Sekolah

### Data Sekolah

Lengkapi data di **Pengaturan** → **Profil Sekolah**:

| Field | Keterangan |
|-------|-----------|
| Nama Sekolah | Misalnya: SMP Negeri 1 Jakarta |
| NPSN | Nomor Pokok Sekolah Nasional |
| Tipe Sekolah | SD/SMP/SMA/SMK/MI/MTs/MA |
| Alamat | Alamat lengkap sekolah |
| Telepon | Nomor telepon sekolah |
| Email | Email resmi sekolah |
| Nama Kepala Sekolah | Untuk tanda tangan di rapor |

Data ini digunakan otomatis di seluruh dokumen dan tampilan.

---

## 4. Domain Kustom

### Subdomain (Default)

Setiap sekolah mendapat subdomain otomatis:
```
https://[slug-sekolah].edusaas.id
```

Contoh: `https://smpn1jakarta.edusaas.id`

### Domain Sendiri (Paket Enterprise)

Sekolah dengan paket Enterprise bisa menggunakan domain sendiri.

#### Langkah Setup:

1. **Di DNS domain sekolah**, tambah CNAME record:
   ```
   CNAME  app.sekolahanda.sch.id  →  custom.edusaas.id
   ```

2. **Hubungi Super Admin** untuk mendaftarkan domain di platform

3. **Konfigurasi SSL** akan dilakukan otomatis via Let's Encrypt

4. **Di Super Admin Panel**, update tenant:
   - Tambah domain kustom di pengaturan tenant
   - Pastikan DNS sudah propagasi

---

## 5. Template Notifikasi

### Kustomisasi Pesan WhatsApp

Setiap sekolah bisa mengubah template pesan di **Pengaturan** → **Template Notifikasi**:

| Template | Variabel yang Tersedia |
|----------|----------------------|
| `spp_reminder` | `{{student_name}}`, `{{period}}`, `{{amount}}`, `{{due_date}}` |
| `absen_alfa` | `{{student_name}}`, `{{date}}`, `{{subject}}` |
| `rapor_ready` | `{{student_name}}`, `{{semester}}`, `{{academic_year}}` |
| `ppdb_status` | `{{parent_name}}`, `{{registration_number}}`, `{{student_name}}`, `{{status}}` |

Contoh template:
```
Yth. Orang Tua/Wali {{student_name}},
SPP bulan {{period}} sebesar Rp {{amount}} belum dibayar.
Jatuh tempo: {{due_date}}.
Silakan lakukan pembayaran.
Terima kasih - SMP Negeri 1 Jakarta
```

---

## 6. Bahasa

### Mengubah Bahasa Default

Edit `.env`:
```env
APP_LOCALE=id      # Bahasa Indonesia (default)
APP_LOCALE=en      # English
```

### Menambah Terjemahan

Tambah file terjemahan di folder `lang/`:
- `lang/id.json` - Bahasa Indonesia
- `lang/en.json` - English

Untuk menambah bahasa baru (contoh: Bahasa Jawa):
1. Buat file `lang/jv.json`
2. Isi terjemahan dengan format JSON key-value

---

## 7. Kustomisasi Rapor

### Format Rapor

Template rapor berada di:
```
resources/views/reports/rapor.blade.php
```

Elemen yang bisa dikustomisasi:
- Header (logo, nama sekolah, alamat)
- Format tabel nilai
- Skala penilaian (A-E atau 0-100)
- Catatan wali kelas
- Tanda tangan (Kepala Sekolah, Wali Kelas)

### Pengaturan Penilaian

Di **Pengaturan** → **Akademik**:
- Bobot per tipe penilaian (Tugas, UH, PTS, PAS)
- KKM (Kriteria Ketuntasan Minimal) per mata pelajaran
- Format nilai (angka/huruf)

---

## 8. Kustomisasi Halaman PPDB

### Halaman Publik PPDB

Halaman PPDB (`/ppdb`) bisa dikustomisasi:

Template: `resources/views/ppdb/`
- `index.blade.php` - Halaman utama PPDB
- `register.blade.php` - Formulir pendaftaran
- `status.blade.php` - Cek status pendaftaran

Data yang tampil otomatis menyesuaikan tenant:
- Logo dan nama sekolah
- Gelombang pendaftaran aktif
- Persyaratan
- Kuota

---

## 9. Modul per Paket

Fitur yang tersedia tergantung paket langganan:

| Fitur | Starter | Professional | Enterprise |
|-------|---------|-------------|-----------|
| Kehadiran | ✅ | ✅ | ✅ |
| Nilai & Rapor | ✅ | ✅ | ✅ |
| SPP | ✅ | ✅ | ✅ |
| Pengumuman | ✅ | ✅ | ✅ |
| PPDB | ❌ | ✅ | ✅ |
| Perpustakaan | ❌ | ✅ | ✅ |
| Aset & Inventaris | ❌ | ✅ | ✅ |
| Laporan Lanjutan | ❌ | ✅ | ✅ |
| WhatsApp | ❌ | ✅ | ✅ |
| API Access | ❌ | ❌ | ✅ |
| Domain Kustom | ❌ | ❌ | ✅ |
| Support Prioritas | ❌ | ❌ | ✅ |

Untuk menambah fitur baru ke paket, edit data Plan via Super Admin Panel.

---

## 10. Developer: Menambah Modul Baru

### Struktur

```
app/
├── Models/NewModule.php
├── Filament/SchoolAdmin/Resources/NewModuleResource.php
├── Http/Controllers/Api/NewModuleController.php
database/
├── migrations/xxxx_create_new_modules_table.php
```

### Langkah:

1. Buat migration dan model
2. Tambahkan trait `BelongsToTenant` di model
3. Buat Filament Resource untuk panel admin
4. Daftarkan di fitur paket jika perlu
5. Tambahkan terjemahan di `lang/id.json`
