# EduSaaS — Database Schema

All tables include: `id`, `tenant_id`, `created_at`, `updated_at`, `deleted_at` (soft delete) unless noted.

---

## Core / Tenancy

```sql
tenants
  id, name, slug (subdomain), logo, phone, email, address, city, province,
  npsn (Nomor Pokok Sekolah Nasional), school_type (SD/SMP/SMA/SMK),
  principal_name, status (trial/active/suspended/expired),
  trial_ends_at, subscription_id, settings (json), created_at, updated_at

plans
  id, name, slug, price_monthly, price_annual, max_students, max_teachers,
  features (json array), is_active, sort_order

subscriptions
  id, tenant_id, plan_id, starts_at, ends_at, status, payment_method,
  auto_renew, notes

users
  id, tenant_id (nullable for super_admin), name, email, password,
  avatar, phone, type (super_admin/school_admin/teacher/student/parent),
  is_active, last_login_at, email_verified_at
```

---

## Academic Structure

```sql
academic_years
  id, tenant_id, name (e.g. 2025/2026), starts_at, ends_at, is_active

semesters
  id, tenant_id, academic_year_id, name (Ganjil/Genap),
  starts_at, ends_at, is_active

grades (tingkat)
  id, tenant_id, name (e.g. Kelas 7), level (7/8/9/10/11/12),
  sort_order

classrooms
  id, tenant_id, grade_id, academic_year_id, name (e.g. 7A),
  homeroom_teacher_id, capacity, room_name

subjects
  id, tenant_id, name, code, type (teori/praktek/muatan_lokal),
  color, icon, description

classroom_subjects
  id, tenant_id, classroom_id, subject_id, teacher_id,
  hours_per_week, academic_year_id, semester_id

curriculum_settings
  id, tenant_id, academic_year_id, assessment_weights (json),
  kkm_default, passing_grade, grading_type (angka/huruf/predikat)
```

---

## Students

```sql
students
  id, tenant_id, user_id, nis, nisn, classroom_id, academic_year_id,
  full_name, nickname, gender (L/P), birth_place, birth_date, religion,
  nationality, child_order, num_siblings, address, rt, rw, village,
  district, city, postal_code, province, phone, email, photo,
  blood_type, height, weight, disabilities, hobbies,
  previous_school, status (active/alumni/transferred/dropped),
  entry_year, graduation_year, notes

student_parents
  id, tenant_id, student_id, relation (ayah/ibu/wali), name, nik,
  birth_date, education, occupation, income, phone, email, address,
  is_emergency_contact, is_whatsapp_active

student_documents
  id, tenant_id, student_id, type (akta/ijazah/kk/foto/etc),
  file_path, file_name, verified_at, notes
```

---

## PPDB

```sql
ppdb_waves
  id, tenant_id, academic_year_id, name, quota_per_class,
  opens_at, closes_at, requirements (json), is_active

ppdb_registrations
  id, tenant_id, ppdb_wave_id, registration_number, full_name,
  birth_date, gender, parent_name, parent_phone, parent_email,
  previous_school, address, status (pending/accepted/rejected/waitlist),
  notes, reviewed_by, reviewed_at, documents (json),
  student_id (set after acceptance)
```

---

## Teachers

```sql
teachers
  id, tenant_id, user_id, nip, nuptk, full_name, gender,
  birth_place, birth_date, religion, employment_status (PNS/GTT/GTY/Honorer),
  grade_group, position, education, major, phone, email, photo,
  joined_at, is_homeroom_teacher, homeroom_classroom_id, notes

teaching_schedules
  id, tenant_id, teacher_id, classroom_subject_id, day_of_week (1-7),
  start_time, end_time, room, semester_id, is_active
```

---

## Attendance

```sql
attendance_sessions
  id, tenant_id, classroom_subject_id, teacher_id, date,
  start_time, end_time, topic, qr_token, qr_expires_at,
  qr_generated_at, status (open/closed), notes

student_attendances
  id, tenant_id, attendance_session_id, student_id,
  status (hadir/sakit/izin/alfa/terlambat), check_in_time,
  method (qr/manual), notes, notified_parent_at

teacher_attendances
  id, tenant_id, teacher_id, date, check_in_time, check_out_time,
  method (qr/fingerprint/manual), location_lat, location_lng,
  status (hadir/sakit/izin/alfa/terlambat/pulang_cepat), notes
```

---

## Grades & Rapor

```sql
assessment_types
  id, tenant_id, name, code (TGS/UH/PTS/PAS/PROJ), default_weight,
  count_for_final (bool)

assessments
  id, tenant_id, classroom_subject_id, assessment_type_id, semester_id,
  name, date, max_score, weight_override, notes

student_grades
  id, tenant_id, assessment_id, student_id, score, is_remedial,
  remedial_score, notes, graded_by

report_cards
  id, tenant_id, student_id, semester_id, classroom_id,
  homeroom_comment, principal_comment, status (draft/published),
  published_at, downloaded_at

report_card_subjects
  id, tenant_id, report_card_id, subject_id, final_score,
  letter_grade, predicate (A/B/C/D), description,
  hadir, sakit, izin, alfa

extracurriculars
  id, tenant_id, name, description, teacher_id, schedule

student_extracurriculars
  id, tenant_id, student_id, extracurricular_id, academic_year_id,
  score, description
```

---

## Finance

```sql
spp_types
  id, tenant_id, name, code, amount, frequency (monthly/one_time/annual),
  applies_to (all/per_grade/per_student), description

spp_discounts
  id, tenant_id, student_id (nullable), grade_id (nullable),
  name, type (percent/fixed), value, valid_from, valid_until, notes

spp_bills
  id, tenant_id, student_id, spp_type_id, period (YYYY-MM for monthly),
  amount, discount_amount, final_amount, due_date,
  status (unpaid/partial/paid/overdue/waived), notes

payments
  id, tenant_id, student_id, reference_number, amount, payment_date,
  method (cash/transfer/midtrans/xendit), gateway_transaction_id,
  gateway_status, receipt_path, recorded_by, notes

payment_bill_allocations
  id, payment_id, spp_bill_id, amount

finance_accounts
  id, tenant_id, name, type (income/expense), balance

finance_transactions
  id, tenant_id, finance_account_id, type, amount, description,
  date, reference, recorded_by
```

---

## Communication

```sql
announcements
  id, tenant_id, title, content (longtext), author_id,
  target_type (all/grade/classroom/role), target_ids (json),
  attachments (json), is_pinned, published_at, expires_at

announcement_reads
  id, announcement_id, user_id, read_at

messages
  id, tenant_id, thread_id, sender_id, content, attachments (json), read_at

whatsapp_logs
  id, tenant_id, to_number, template, message, status (sent/failed/delivered),
  sent_at, error_message, reference_type, reference_id

notification_templates
  id, tenant_id, key (spp_reminder/absen_alfa/rapor_ready/etc),
  subject, body (with {variables}), is_active, channel (wa/email/both)
```

---

## Library

```sql
book_categories
  id, tenant_id, name, description

books
  id, tenant_id, isbn, title, author, publisher, year, category_id,
  stock, available_stock, location, cover_path, description

book_loans
  id, tenant_id, book_id, borrower_id, borrower_type (student/teacher),
  loan_date, due_date, return_date, status (borrowed/returned/overdue/lost),
  fine_amount, fine_paid, notes
```

---

## Inventory

```sql
asset_categories
  id, tenant_id, name, description

assets
  id, tenant_id, code, name, category_id, condition (good/minor_damage/major_damage/lost),
  location, quantity, value, acquisition_date, photo,
  description, notes

facilities
  id, tenant_id, name, type, capacity, location, status, description

facility_bookings
  id, tenant_id, facility_id, booked_by, date, start_time, end_time,
  purpose, status (pending/approved/rejected), notes
```

---

## Indexes (Critical for Performance)

```sql
-- All tenant-scoped queries
INDEX idx_tenant ON all_tables (tenant_id)

-- Student queries
INDEX idx_student_classroom ON students (classroom_id, academic_year_id)
INDEX idx_student_status ON students (tenant_id, status)

-- Attendance
INDEX idx_attendance_date ON student_attendances (attendance_session_id, student_id)
INDEX idx_session_date ON attendance_sessions (tenant_id, date)

-- Finance
INDEX idx_bill_student ON spp_bills (student_id, status, period)
INDEX idx_payment_date ON payments (tenant_id, payment_date)

-- Grades
INDEX idx_grade_student ON student_grades (student_id, assessment_id)
```
