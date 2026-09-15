# PR #29 — Izin Online, Tabungan & Cashless Siswa, BK (Poin Pelanggaran & Konseling), Otomasi Tagihan per Sekolah

## Ringkasan

Melengkapi operasional harian sekolah (setara modul "Smart Cashless", "Portal Ortu & Siswa" pada platform sejenis) sekaligus memperbaiki job terjadwal yang tidak aman untuk multi-sekolah.

## Fitur Baru

### 1. Izin / Sakit Online
- Orang tua & siswa mengajukan izin/sakit dari portal (rentang tanggal, alasan, lampiran surat dokter PDF/JPG di **disk privat**).
- Wali kelas mendapat notifikasi di panel guru (menu **Izin Siswa** dengan badge), menyetujui/menolak dengan catatan; admin sekolah juga dapat mengelola semua pengajuan.
- Persetujuan **otomatis mencatat absensi "sakit/izin"** pada semua sesi kelas di tanggal tersebut, dan dialog **Isi Absensi** mengisi status izin/sakit secara default untuk sesi yang dibuat kemudian.
- Pemohon menerima pesan hasil tinjauan di menu Pesan portal.
- Lampiran hanya bisa dibuka staf sekolah, wali kelas siswa, atau pemohon.

### 2. Tabungan & Cashless Siswa
- Setoran, penarikan, dan pembelian cashless (kantin/koperasi) dengan **saldo berjalan**.
- Transaksi tidak bisa diubah/dihapus; dicatat lewat `SavingsService` yang mengunci baris siswa sehingga saldo tidak pernah negatif atau rusak pada transaksi bersamaan.
- Portal siswa/orang tua: saldo, pemasukan & pengeluaran bulan ini, riwayat transaksi.

### 3. Bimbingan Konseling (BK)
- **Tata Tertib** (jenis pelanggaran, tingkat, poin), **Catatan Pelanggaran** (poin tersimpan per kejadian, tindakan, ringkasan total poin), **Catatan Konseling** (kategori, tindak lanjut, rahasia/dibagikan).
- Guru dapat melaporkan pelanggaran siswa di kelas yang diajar/diwalikan.
- Portal: total poin tahun ajaran, riwayat pelanggaran yang boleh dilihat orang tua, prestasi siswa, dan catatan konseling yang dibagikan — catatan rahasia & pelanggaran tersembunyi **tidak pernah tampil**.

## Perbaikan Job Terjadwal

| Job | Masalah | Perbaikan |
|---|---|---|
| `GenerateMonthlyBills` | Membuat tagihan untuk **semua** jenis SPP setiap bulan (termasuk per semester/sekali bayar), mengabaikan potongan/beasiswa, tanpa konteks tenant | Hanya jenis `monthly`, menerapkan potongan per siswa/per tingkat, tanggal jatuh tempo dari pengaturan (`spp_due_day`, default 10), status `waived` bila 100% potongan, berjalan dalam tenant |
| `SendOverdueSppReminders` | Satu WhatsApp per tagihan (spam), format mata uang hardcode, bisa memakai template sekolah lain | Satu pesan per siswa berisi semua periode & total, `CurrencyHelper`, berjalan dalam tenant, termasuk status `partial` |
| Command penjadwal | Sekolah status **trial** tidak diproses; periode tidak divalidasi | Mencakup `trial`, validasi format `YYYY-MM` |

## Bug Kritis: Semua Job Antrean Gagal (Import/Export, Notifikasi)

`config/multitenancy.php` mengaktifkan `queues_are_tenant_aware_by_default`, padahal aplikasi memakai model `App\Models\Tenant` sendiri, bukan milik paket spatie. Akibatnya **setiap job di worker antrean gagal** dengan `CurrentTenantCouldNotBeDeterminedInTenantAwareJob` — termasuk **import/export Excel siswa, guru, jadwal, mapel, dan nilai** (catatan calon client) serta notifikasi database Filament. Selain itu, job yang berjalan tanpa konteks sekolah berisiko mengekspor data semua sekolah atau menimpa data siswa sekolah lain saat import (pencarian `nis` tanpa scope).

Perbaikan:
- `App\Support\QueueTenancy`: ID sekolah aktif disimpan di payload setiap job dan dipulihkan saat worker memprosesnya; tenant sebelumnya dikembalikan setelah job selesai (aman untuk `QUEUE_CONNECTION=sync`).
- Fitur antrean paket spatie dinonaktifkan.
- Job yang mengatur tenant sendiri kini mengembalikan tenant sebelumnya (tidak menghapusnya).
- `QueueTenancyTest` sebagai regresi.

## Data Demo
8 tata tertib, 7 catatan pelanggaran, 4 catatan konseling (rahasia & dibagikan), riwayat tabungan/kantin kelas 7A, dan pengajuan izin (disetujui — absensi otomatis tercatat, menunggu, ditolak).

## Test
`StudentServicesTest`: pengajuan izin + persetujuan mengubah absensi, orang tua tidak bisa mengajukan untuk anak lain, lampiran privat, saldo berjalan & tidak boleh negatif, catatan rahasia/tersembunyi tidak tampil di portal, halaman portal baru 200.

## Dampak
- Migrasi baru: `leave_requests`, `savings_transactions`, `violation_types`, `student_violations`, `counseling_notes`.
- Menu baru: Kehadiran → Izin Siswa; Keuangan → Tabungan & Cashless; Kesiswaan → Tata Tertib, Catatan Pelanggaran, Konseling (BK); Panel Guru → Izin Siswa, Laporan Pelanggaran; Portal → Izin, Tabungan, Kedisiplinan & Konseling.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
