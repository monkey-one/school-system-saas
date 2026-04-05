# EduSaaS — Complete Feature List

## 🏫 Multi-Tenancy SaaS
- Subdomain-based tenant isolation (sekolah1.edusaas.id)
- Separate panel per school (School Admin, Teacher, Student, Parent)
- Plan-based feature flags (Free / Starter / Pro / Enterprise)
- Super Admin central dashboard for SaaS management
- Tenant impersonation (masuk ke panel sekolah)
- Usage analytics per tenant
- Automated trial period management

## 📋 PPDB Online (Penerimaan Peserta Didik Baru)
- Public registration form (no login needed)
- Multi-wave/batch PPDB management
- Auto-generated registration number
- Document upload checklist (akta, ijazah, KK, foto)
- Status tracking by registration number
- Bulk accept/reject with auto-account creation
- Acceptance letter PDF generation
- WhatsApp notification on status change
- Quota management per classroom per wave

## 👨‍🎓 Manajemen Siswa
- Complete student profiles with photo
- NIS and NISN management
- Parent/guardian data (father, mother, guardian)
- Multi-document management per student
- Student status tracking (aktif/alumni/pindah/keluar)
- Classroom assignment management
- Annual class promotion (naik kelas bulk)
- Alumni database with graduation year
- Import students via Excel template

## 👨‍🏫 Manajemen Guru & Pegawai
- Teacher profiles with NUPTK/NIP
- Employment status (PNS/GTT/GTY/Honorer)
- Teaching assignment per subject/class
- Weekly schedule builder with conflict detection
- Workload report (jam mengajar per week)
- Teacher document management

## 📅 Jadwal Pelajaran
- Weekly schedule per classroom
- Subject-teacher-classroom assignment
- Room/facility assignment
- Schedule conflict detection
- Export jadwal to PDF
- iCal integration

## 📊 Absensi QR Code
- Teacher generates QR per class session (valid 15 min)
- Student scan via mobile browser (no app needed)
- Auto late detection (terlambat)
- Manual attendance override by teacher
- Teacher attendance with GPS check-in
- Monthly attendance recap
- Attendance report per student
- Print daftar hadir PDF
- WhatsApp alert to parent on alfa
- Attendance analytics per classroom

## 📝 Penilaian & Rapor
- Multi-type assessments (Tugas, UH, PTS, PAS, Proyek)
- Custom assessment weights per curriculum
- Bulk grade input by subject teacher
- Excel import for grades
- Auto final grade calculation
- Remedial tracking and scoring
- Kurikulum Merdeka grade format
- Rapor generation per student (PDF)
- Batch rapor print for entire classroom
- WhatsApp rapor delivery to parents
- Student ranking per classroom
- KKM tracking and below-KKM flags
- Extracurricular (ekskul) grades
- Homeroom teacher comments

## 💰 Keuangan SPP
- Multiple SPP types (bulanan, uang gedung, seragam, dll)
- Auto-generate monthly bills for all students
- Scholarship/discount management
- Online payment via Midtrans (QRIS, GoPay, OVO, VA, Credit Card)
- Online payment via Xendit (alternative)
- Cash/transfer manual payment entry
- Payment receipt PDF (auto WhatsApp)
- Overdue reminders (WhatsApp every Monday)
- Finance dashboard (tagihan, terbayar, tunggakan)
- Monthly income charts
- Export laporan keuangan to Excel
- Student outstanding balance report
- Petty cash (kas kecil) tracking

## 📢 Komunikasi
- Rich text announcements with file attachments
- Target audience: all/per grade/per classroom/per role
- Scheduled announcements
- WhatsApp blast to groups
- Internal direct messaging
- Announcement read receipts
- Pinned announcements
- Notification templates (customizable)

## 📱 WhatsApp Integration (Fonnte API)
- Auto notifications:
  - Absen alfa → parent
  - SPP due reminder → parent
  - SPP payment received → parent
  - PPDB status → applicant
  - Rapor ready → parent
  - Custom announcements
- WA number management per contact
- Bulk WA blast with variables
- Delivery status tracking
- Queue-based sending (no timeout issues)
- Daily quota monitoring
- Failed message retry

## 📚 Perpustakaan
- Book catalog management (ISBN, cover, category)
- QR/barcode checkout
- Loan rules (max books, max days)
- Overdue fine calculation and payment
- Popular books report
- Real-time available stock
- Book return management
- Student/teacher loan history
- Book purchase request feature
- Export catalog to Excel

## 🏢 Inventaris & Fasilitas
- Asset registry with QR label printing
- Condition tracking (baik/rusak ringan/rusak berat)
- Maintenance log
- Depreciation calculation
- Facility (ruangan) management
- Facility booking calendar
- Asset audit report

## 🌐 Portal Siswa (Self-Service)
- Dashboard: jadwal hari ini, nilai, tagihan, pengumuman
- Riwayat kehadiran per bulan
- Nilai per mapel per semester
- Download rapor PDF
- Bayar SPP online
- Baca pengumuman
- Riwayat peminjaman perpustakaan

## 👨‍👩‍👧 Portal Orang Tua
- Monitor semua anak (1 parent = multiple students)
- Real-time kehadiran anak
- Nilai dan perkembangan
- Download rapor
- Bayar SPP online
- Chat dengan wali kelas

## 🔌 REST API
- Laravel Sanctum authentication
- Student, attendance, grades, SPP endpoints
- Mobile app ready
- Rate limiting
- OpenAPI/Swagger documentation
- Versioning (v1)

## 🔐 Keamanan
- Role-based access control (6 roles)
- Tenant data isolation
- Rate limiting on auth
- File upload validation
- Audit log on sensitive actions
- CSRF protection
- Two-factor authentication (2FA) support

## ⚙️ Pengaturan Sekolah
- School profile (nama, logo, NPSN, alamat)
- Academic year management
- Assessment weight configuration per curriculum
- KKM setting per subject
- WA notification toggle per event
- Payment gateway configuration
- School features toggle

## 📈 Analytics & Laporan
- Student enrollment trend
- Attendance analytics (per class, per student)
- Finance: revenue chart, collection rate
- Grade distribution per subject
- PPDB funnel analytics
- SaaS: MRR, ARR, tenant count

## 🛠️ Developer / Deployment
- Docker Compose included
- `.env.example` fully documented
- Demo seeder (150 students, full data)
- PHPUnit test suite
- Laravel Horizon for queue monitoring
- Telescope for debugging (dev only)
- Complete installation guide
- User manual (PDF + web)
