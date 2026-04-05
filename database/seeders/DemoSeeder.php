<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Enums\AttendanceStatus;
use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PPDBStatus;
use App\Enums\Religion;
use App\Enums\SchoolType;
use App\Enums\StudentStatus;
use App\Enums\SubjectType;
use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AttendanceSession;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\GradeLevel;
use App\Models\NotificationTemplate;
use App\Models\Payment;
use App\Models\PaymentBillAllocation;
use App\Models\Plan;
use App\Models\PPDBRegistration;
use App\Models\PPDBWave;
use App\Models\Semester;
use App\Models\SppBill;
use App\Models\SppType;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\StudentParent;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    private array $maleFirstNames = [
        'Ahmad', 'Muhammad', 'Rizky', 'Dimas', 'Andi', 'Budi', 'Cahyo', 'Dwi',
        'Eko', 'Fajar', 'Galih', 'Hadi', 'Irfan', 'Joko', 'Krisna', 'Lukman',
        'Maulana', 'Naufal', 'Omar', 'Prasetyo', 'Rafi', 'Satria', 'Teguh',
        'Umar', 'Vino', 'Wahyu', 'Yoga', 'Zaki', 'Arif', 'Bagas',
        'Deni', 'Faisal', 'Gilang', 'Hendra', 'Ilham', 'Jefri', 'Kevin',
        'Luthfi', 'Mukhlis', 'Nanda', 'Putra', 'Rangga', 'Surya', 'Taufik',
        'Wira', 'Yusuf', 'Aditya', 'Bagus', 'Danu', 'Firman', 'Hanif',
    ];

    private array $femaleFirstNames = [
        'Siti', 'Nur', 'Dewi', 'Anisa', 'Putri', 'Rina', 'Sri', 'Wulan',
        'Yuni', 'Zahra', 'Ayu', 'Bunga', 'Citra', 'Dian', 'Eka', 'Fitri',
        'Gita', 'Hana', 'Indah', 'Jasmine', 'Kartika', 'Lestari', 'Mega',
        'Nadia', 'Oktavia', 'Pratiwi', 'Ratna', 'Sari', 'Tika', 'Ulfa',
        'Vera', 'Widya', 'Yanti', 'Amelia', 'Bella', 'Cantika', 'Della',
        'Eva', 'Farah', 'Gina', 'Halimah', 'Intan', 'Julia', 'Kirana',
        'Laila', 'Mawar', 'Nabila', 'Olivia', 'Puspita', 'Rahma', 'Salsa',
    ];

    private array $lastNames = [
        'Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Hidayat', 'Nugraha', 'Santoso',
        'Wibowo', 'Setiawan', 'Purnama', 'Permana', 'Ramadhan', 'Firmansyah',
        'Kurniawan', 'Utama', 'Aditya', 'Surya', 'Putra', 'Mahendra', 'Gunawan',
        'Suryani', 'Handayani', 'Lestari', 'Rahayu', 'Wati', 'Sari', 'Anggraini',
        'Puspitasari', 'Fitriani', 'Wahyuni', 'Hartono', 'Susanto', 'Budiman',
        'Halim', 'Iskandar', 'Mulyadi', 'Sudirman', 'Hakim', 'Fauzi', 'Syahputra',
    ];

    private array $cities = [
        'Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta', 'Bekasi',
        'Tangerang', 'Depok', 'Bogor', 'Malang',
    ];

    public function run(): void
    {
        $this->command->info('🌱 Seeding Plans...');
        $plans = $this->seedPlans();

        $this->command->info('🌱 Seeding Super Admin...');
        $this->seedSuperAdmin();

        $this->command->info('🌱 Seeding Demo Tenant...');
        $tenant = $this->seedDemoTenant($plans['professional']);

        Tenant::setCurrent($tenant);

        $this->command->info('🌱 Seeding School Admin...');
        $admin = $this->seedSchoolAdmin($tenant);

        $this->command->info('🌱 Seeding Academic Year & Semesters...');
        [$academicYear, $semester1, $semester2] = $this->seedAcademicYear($tenant);

        $this->command->info('🌱 Seeding Grade Levels...');
        $gradeLevels = $this->seedGradeLevels($tenant);

        $this->command->info('🌱 Seeding Classrooms...');
        $classrooms = $this->seedClassrooms($tenant, $gradeLevels, $academicYear);

        $this->command->info('🌱 Seeding Subjects...');
        $subjects = $this->seedSubjects($tenant);

        $this->command->info('🌱 Seeding Teachers...');
        $teachers = $this->seedTeachers($tenant, $subjects, $classrooms);

        $this->command->info('🌱 Seeding Students...');
        $students = $this->seedStudents($tenant, $classrooms, $academicYear);

        $this->command->info('🌱 Seeding Parents...');
        $this->seedParents($tenant, $students);

        $this->command->info('🌱 Seeding Classroom Subjects...');
        $classroomSubjects = $this->seedClassroomSubjects($tenant, $classrooms, $subjects, $teachers, $academicYear, $semester1);

        $this->command->info('🌱 Seeding PPDB...');
        $this->seedPPDB($tenant, $academicYear);

        $this->command->info('🌱 Seeding Assessment Types & Assessments...');
        $this->seedAssessments($tenant, $classroomSubjects, $semester1, $students, $admin);

        $this->command->info('🌱 Seeding SPP...');
        $this->seedSpp($tenant, $students, $admin);

        $this->command->info('🌱 Seeding Announcements...');
        $this->seedAnnouncements($tenant, $admin);

        $this->command->info('🌱 Seeding Books...');
        $this->seedBooks($tenant);

        $this->command->info('🌱 Seeding Assets...');
        $this->seedAssets($tenant);

        $this->command->info('🌱 Seeding Notification Templates...');
        $this->seedNotificationTemplates($tenant);

        Tenant::forgetCurrent();

        $this->command->info('✅ Demo seeding completed!');
    }

    private function seedPlans(): array
    {
        $starter = Plan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'price_monthly' => 500000,
                'price_annual' => 5000000,
                'max_students' => 200,
                'max_teachers' => 20,
                'features' => ['attendance', 'grades', 'spp', 'announcements'],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $professional = Plan::updateOrCreate(
            ['slug' => 'professional'],
            [
                'name' => 'Professional',
                'price_monthly' => 1000000,
                'price_annual' => 10000000,
                'max_students' => 500,
                'max_teachers' => 50,
                'features' => ['attendance', 'grades', 'spp', 'announcements', 'ppdb', 'library', 'assets', 'reports', 'whatsapp'],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $enterprise = Plan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise',
                'price_monthly' => 2000000,
                'price_annual' => 20000000,
                'max_students' => 2000,
                'max_teachers' => 200,
                'features' => ['attendance', 'grades', 'spp', 'announcements', 'ppdb', 'library', 'assets', 'reports', 'whatsapp', 'api', 'custom_domain', 'priority_support'],
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        return compact('starter', 'professional', 'enterprise');
    }

    private function seedSuperAdmin(): User
    {
        return User::updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'superadmin@edusaas.id')],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'password')),
                'type' => UserType::SUPER_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedDemoTenant(Plan $plan): Tenant
    {
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'demo'],
            [
                'name' => 'SMP Negeri 1 Demo',
                'phone' => '021-5551234',
                'email' => 'info@smpn1demo.id',
                'address' => 'Jl. Pendidikan No. 1',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'npsn' => '20100001',
                'school_type' => SchoolType::SMP,
                'principal_name' => 'Dr. Hadi Santoso, M.Pd.',
                'status' => TenantStatus::ACTIVE,
                'settings' => [
                    'color_primary' => '#1e40af',
                    'color_secondary' => '#f59e0b',
                    'late_threshold_minutes' => 15,
                ],
            ]
        );

        Subscription::updateOrCreate(
            ['tenant_id' => $tenant->id, 'plan_id' => $plan->id],
            [
                'starts_at' => now()->startOfYear(),
                'ends_at' => now()->endOfYear(),
                'status' => 'active',
                'auto_renew' => true,
            ]
        );

        return $tenant;
    }

    private function seedSchoolAdmin(Tenant $tenant): User
    {
        return User::updateOrCreate(
            ['email' => 'admin@smpn1demo.id'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Admin SMP Negeri 1 Demo',
                'password' => Hash::make('password'),
                'type' => UserType::SCHOOL_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedAcademicYear(Tenant $tenant): array
    {
        $academicYear = AcademicYear::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => '2025/2026'],
            [
                'starts_at' => Carbon::create(2025, 7, 14),
                'ends_at' => Carbon::create(2026, 6, 20),
                'is_active' => true,
            ]
        );

        $semester1 = Semester::updateOrCreate(
            ['tenant_id' => $tenant->id, 'academic_year_id' => $academicYear->id, 'name' => 'Ganjil'],
            [
                'starts_at' => Carbon::create(2025, 7, 14),
                'ends_at' => Carbon::create(2025, 12, 20),
                'is_active' => true,
            ]
        );

        $semester2 = Semester::updateOrCreate(
            ['tenant_id' => $tenant->id, 'academic_year_id' => $academicYear->id, 'name' => 'Genap'],
            [
                'starts_at' => Carbon::create(2026, 1, 5),
                'ends_at' => Carbon::create(2026, 6, 20),
                'is_active' => false,
            ]
        );

        return [$academicYear, $semester1, $semester2];
    }

    private function seedGradeLevels(Tenant $tenant): array
    {
        $grades = [];
        foreach ([7, 8, 9] as $level) {
            $grades[$level] = GradeLevel::updateOrCreate(
                ['tenant_id' => $tenant->id, 'level' => $level],
                [
                    'name' => "Kelas {$level}",
                    'sort_order' => $level,
                ]
            );
        }
        return $grades;
    }

    private function seedClassrooms(Tenant $tenant, array $gradeLevels, AcademicYear $academicYear): array
    {
        $classrooms = [];
        foreach ($gradeLevels as $level => $grade) {
            foreach (['A', 'B', 'C'] as $suffix) {
                $name = "{$level}{$suffix}";
                $classrooms[$name] = Classroom::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'name' => $name, 'academic_year_id' => $academicYear->id],
                    [
                        'grade_id' => $grade->id,
                        'capacity' => 32,
                    ]
                );
            }
        }
        return $classrooms;
    }

    private function seedSubjects(Tenant $tenant): array
    {
        $subjectData = [
            ['name' => 'Matematika', 'code' => 'MTK', 'type' => SubjectType::TEORI],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'type' => SubjectType::TEORI],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'type' => SubjectType::TEORI],
            ['name' => 'IPA', 'code' => 'IPA', 'type' => SubjectType::TEORI],
            ['name' => 'IPS', 'code' => 'IPS', 'type' => SubjectType::TEORI],
            ['name' => 'PKN', 'code' => 'PKN', 'type' => SubjectType::TEORI],
            ['name' => 'Pendidikan Agama', 'code' => 'PAI', 'type' => SubjectType::TEORI],
            ['name' => 'Seni Budaya', 'code' => 'SBK', 'type' => SubjectType::PRAKTEK],
            ['name' => 'PJOK', 'code' => 'PJK', 'type' => SubjectType::PRAKTEK],
            ['name' => 'Prakarya', 'code' => 'PKY', 'type' => SubjectType::PRAKTEK],
            ['name' => 'TIK', 'code' => 'TIK', 'type' => SubjectType::PRAKTEK],
            ['name' => 'Bahasa Daerah', 'code' => 'BDA', 'type' => SubjectType::MUATAN_LOKAL],
        ];

        $subjects = [];
        foreach ($subjectData as $data) {
            $subjects[$data['code']] = Subject::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $data['code']],
                [
                    'name' => $data['name'],
                    'type' => $data['type'],
                ]
            );
        }
        return $subjects;
    }

    private function seedTeachers(Tenant $tenant, array $subjects, array $classrooms): array
    {
        $teacherData = [
            ['name' => 'Hadi Santoso', 'gender' => Gender::MALE, 'subject' => 'MTK'],
            ['name' => 'Siti Rahmawati', 'gender' => Gender::FEMALE, 'subject' => 'BIN'],
            ['name' => 'Ahmad Fauzi', 'gender' => Gender::MALE, 'subject' => 'BIG'],
            ['name' => 'Dewi Lestari', 'gender' => Gender::FEMALE, 'subject' => 'IPA'],
            ['name' => 'Budi Prasetyo', 'gender' => Gender::MALE, 'subject' => 'IPS'],
            ['name' => 'Nur Hidayah', 'gender' => Gender::FEMALE, 'subject' => 'PKN'],
            ['name' => 'Muhammad Rizki', 'gender' => Gender::MALE, 'subject' => 'PAI'],
            ['name' => 'Rina Kartika', 'gender' => Gender::FEMALE, 'subject' => 'SBK'],
            ['name' => 'Eko Widodo', 'gender' => Gender::MALE, 'subject' => 'PJK'],
            ['name' => 'Anisa Putri', 'gender' => Gender::FEMALE, 'subject' => 'PKY'],
            ['name' => 'Surya Darma', 'gender' => Gender::MALE, 'subject' => 'TIK'],
            ['name' => 'Wulan Sari', 'gender' => Gender::FEMALE, 'subject' => 'BDA'],
            ['name' => 'Joko Susilo', 'gender' => Gender::MALE, 'subject' => 'MTK'],
            ['name' => 'Indah Permata', 'gender' => Gender::FEMALE, 'subject' => 'BIN'],
            ['name' => 'Teguh Firmansyah', 'gender' => Gender::MALE, 'subject' => 'IPA'],
            ['name' => 'Mega Puspita', 'gender' => Gender::FEMALE, 'subject' => 'BIG'],
            ['name' => 'Arif Rahman', 'gender' => Gender::MALE, 'subject' => 'IPS'],
            ['name' => 'Fitri Handayani', 'gender' => Gender::FEMALE, 'subject' => 'PKN'],
            ['name' => 'Wahyu Nugroho', 'gender' => Gender::MALE, 'subject' => 'PAI'],
            ['name' => 'Citra Dewi', 'gender' => Gender::FEMALE, 'subject' => 'SBK'],
        ];

        $teachers = [];
        $classroomKeys = array_keys($classrooms);

        foreach ($teacherData as $i => $data) {
            $email = Str::slug($data['name'], '.') . '@smpn1demo.id';
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'type' => UserType::TEACHER,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $isHomeroom = $i < 9;
            $teacher = Teacher::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $user->id],
                [
                    'nip' => '19800' . str_pad($i + 1, 3, '0', STR_PAD_LEFT) . '200501' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'full_name' => $data['name'],
                    'gender' => $data['gender'],
                    'birth_place' => $this->cities[array_rand($this->cities)],
                    'birth_date' => Carbon::create(1980 + ($i % 15), rand(1, 12), rand(1, 28)),
                    'religion' => Religion::ISLAM,
                    'employment_status' => $i < 10 ? EmploymentStatus::PNS : EmploymentStatus::GTT,
                    'position' => $isHomeroom ? 'Wali Kelas' : 'Guru Mata Pelajaran',
                    'education' => 'S1',
                    'major' => $subjects[$data['subject']]->name,
                    'phone' => '0812' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'email' => $email,
                    'joined_at' => Carbon::create(2015 + ($i % 8), 1, 1),
                    'is_homeroom_teacher' => $isHomeroom,
                    'homeroom_classroom_id' => $isHomeroom ? $classrooms[$classroomKeys[$i]]->id : null,
                ]
            );

            if ($isHomeroom) {
                $classrooms[$classroomKeys[$i]]->update(['homeroom_teacher_id' => $teacher->id]);
            }

            $teachers[] = ['teacher' => $teacher, 'subject_code' => $data['subject']];
        }

        return $teachers;
    }

    private function seedStudents(Tenant $tenant, array $classrooms, AcademicYear $academicYear): array
    {
        $students = [];
        $classroomKeys = array_keys($classrooms);
        $studentIndex = 0;

        foreach ($classroomKeys as $cidx => $classroomName) {
            $count = ($cidx < 3) ? 18 : (($cidx < 6) ? 17 : 16);
            // Distribute ~150 students: 18*3 + 17*3 + 16*3 = 54+51+48 = 153, adjust
            if ($studentIndex >= 150) {
                break;
            }

            for ($j = 0; $j < $count && $studentIndex < 150; $j++) {
                $isMale = $studentIndex % 2 === 0;
                $firstName = $isMale
                    ? $this->maleFirstNames[$studentIndex % count($this->maleFirstNames)]
                    : $this->femaleFirstNames[$studentIndex % count($this->femaleFirstNames)];
                $lastName = $this->lastNames[$studentIndex % count($this->lastNames)];
                $fullName = "{$firstName} {$lastName}";
                $nis = sprintf('2025%04d', $studentIndex + 1);
                $nisn = sprintf('00%08d', 30000000 + $studentIndex + 1);

                $student = Student::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'nis' => $nis],
                    [
                        'nisn' => $nisn,
                        'classroom_id' => $classrooms[$classroomName]->id,
                        'academic_year_id' => $academicYear->id,
                        'full_name' => $fullName,
                        'nickname' => $firstName,
                        'gender' => $isMale ? Gender::MALE : Gender::FEMALE,
                        'birth_place' => $this->cities[$studentIndex % count($this->cities)],
                        'birth_date' => Carbon::create(2011 + ($cidx < 3 ? 2 : ($cidx < 6 ? 1 : 0)), rand(1, 12), rand(1, 28)),
                        'religion' => Religion::ISLAM,
                        'address' => 'Jl. Contoh No. ' . ($studentIndex + 1),
                        'city' => $this->cities[$studentIndex % count($this->cities)],
                        'province' => 'DKI Jakarta',
                        'status' => StudentStatus::ACTIVE,
                        'entry_year' => 2025,
                    ]
                );

                $students[] = $student;
                $studentIndex++;
            }
        }

        return $students;
    }

    private function seedParents(Tenant $tenant, array $students): void
    {
        foreach ($students as $student) {
            $fatherLastName = $this->lastNames[array_rand($this->lastNames)];
            StudentParent::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'relation' => 'ayah'],
                [
                    'name' => $this->maleFirstNames[array_rand($this->maleFirstNames)] . ' ' . $fatherLastName,
                    'phone' => '0813' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'occupation' => collect(['PNS', 'Wiraswasta', 'Karyawan Swasta', 'Pedagang', 'TNI/Polri', 'Dokter', 'Guru'])->random(),
                    'education' => collect(['SMA', 'D3', 'S1', 'S2'])->random(),
                    'is_emergency_contact' => true,
                    'is_whatsapp_active' => true,
                ]
            );

            $motherLastName = $this->lastNames[array_rand($this->lastNames)];
            StudentParent::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'relation' => 'ibu'],
                [
                    'name' => $this->femaleFirstNames[array_rand($this->femaleFirstNames)] . ' ' . $motherLastName,
                    'phone' => '0857' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'occupation' => collect(['Ibu Rumah Tangga', 'PNS', 'Guru', 'Dokter', 'Wiraswasta', 'Karyawan Swasta'])->random(),
                    'education' => collect(['SMA', 'D3', 'S1', 'S2'])->random(),
                    'is_emergency_contact' => false,
                    'is_whatsapp_active' => true,
                ]
            );
        }
    }

    private function seedClassroomSubjects(Tenant $tenant, array $classrooms, array $subjects, array $teachers, AcademicYear $academicYear, Semester $semester): array
    {
        $classroomSubjects = [];
        $subjectCodes = array_keys($subjects);

        foreach ($classrooms as $classroom) {
            foreach ($subjectCodes as $code) {
                $teacherEntry = collect($teachers)->firstWhere('subject_code', $code);
                $teacherId = $teacherEntry ? $teacherEntry['teacher']->id : $teachers[0]['teacher']->id;

                $cs = ClassroomSubject::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'classroom_id' => $classroom->id,
                        'subject_id' => $subjects[$code]->id,
                        'semester_id' => $semester->id,
                    ],
                    [
                        'teacher_id' => $teacherId,
                        'hours_per_week' => in_array($code, ['MTK', 'BIN', 'BIG', 'IPA']) ? 5 : 3,
                        'academic_year_id' => $academicYear->id,
                    ]
                );

                $classroomSubjects[] = $cs;
            }
        }

        return $classroomSubjects;
    }

    private function seedPPDB(Tenant $tenant, AcademicYear $academicYear): void
    {
        $wave = PPDBWave::updateOrCreate(
            ['tenant_id' => $tenant->id, 'academic_year_id' => $academicYear->id, 'name' => 'Gelombang 1'],
            [
                'quota_per_class' => 32,
                'opens_at' => Carbon::create(2025, 3, 1),
                'closes_at' => Carbon::create(2025, 6, 30),
                'requirements' => ['Ijazah SD/MI', 'Akta Kelahiran', 'Kartu Keluarga', 'Pas Foto 3x4'],
                'is_active' => true,
            ]
        );

        $statuses = [
            PPDBStatus::ACCEPTED, PPDBStatus::ACCEPTED, PPDBStatus::ACCEPTED, PPDBStatus::ACCEPTED,
            PPDBStatus::REJECTED, PPDBStatus::REJECTED,
            PPDBStatus::PENDING, PPDBStatus::PENDING,
            PPDBStatus::WAITLIST, PPDBStatus::WAITLIST,
        ];

        for ($i = 0; $i < 10; $i++) {
            $isMale = $i % 2 === 0;
            $firstName = $isMale
                ? $this->maleFirstNames[40 + $i]
                : $this->femaleFirstNames[40 + $i];
            $lastName = $this->lastNames[30 + $i];
            $regNumber = sprintf('PPDB-%d-%05d', 2025, $i + 1);

            PPDBRegistration::updateOrCreate(
                ['tenant_id' => $tenant->id, 'registration_number' => $regNumber],
                [
                    'ppdb_wave_id' => $wave->id,
                    'full_name' => "{$firstName} {$lastName}",
                    'birth_date' => Carbon::create(2013, rand(1, 12), rand(1, 28)),
                    'gender' => $isMale ? Gender::MALE : Gender::FEMALE,
                    'parent_name' => $this->maleFirstNames[array_rand($this->maleFirstNames)] . ' ' . $this->lastNames[array_rand($this->lastNames)],
                    'parent_phone' => '0812' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'previous_school' => 'SD Negeri ' . ($i + 1) . ' Jakarta',
                    'address' => 'Jl. PPDB No. ' . ($i + 1) . ', Jakarta',
                    'status' => $statuses[$i],
                ]
            );
        }
    }

    private function seedAssessments(Tenant $tenant, array $classroomSubjects, Semester $semester, array $students, User $admin): void
    {
        $types = [
            ['name' => 'Tugas', 'code' => 'TGS', 'weight' => 15, 'final' => true],
            ['name' => 'Ulangan Harian', 'code' => 'UH', 'weight' => 25, 'final' => true],
            ['name' => 'Penilaian Tengah Semester', 'code' => 'PTS', 'weight' => 25, 'final' => true],
            ['name' => 'Penilaian Akhir Semester', 'code' => 'PAS', 'weight' => 30, 'final' => true],
            ['name' => 'Proyek', 'code' => 'PRY', 'weight' => 5, 'final' => false],
        ];

        $assessmentTypes = [];
        foreach ($types as $type) {
            $assessmentTypes[$type['code']] = AssessmentType::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $type['code']],
                [
                    'name' => $type['name'],
                    'default_weight' => $type['weight'],
                    'count_for_final' => $type['final'],
                ]
            );
        }

        // Create some assessments for the first 3 classroom-subjects (sample)
        $sampleCS = array_slice($classroomSubjects, 0, 3);
        foreach ($sampleCS as $cs) {
            foreach (['TGS', 'UH'] as $typeCode) {
                $assessment = Assessment::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'classroom_subject_id' => $cs->id,
                        'assessment_type_id' => $assessmentTypes[$typeCode]->id,
                        'semester_id' => $semester->id,
                        'name' => $assessmentTypes[$typeCode]->name . ' 1',
                    ],
                    [
                        'date' => Carbon::create(2025, 8, rand(1, 30)),
                        'max_score' => 100,
                    ]
                );

                // Grade first 18 students (first classroom)
                $classroomStudents = array_slice($students, 0, 18);
                foreach ($classroomStudents as $student) {
                    StudentGrade::updateOrCreate(
                        [
                            'tenant_id' => $tenant->id,
                            'assessment_id' => $assessment->id,
                            'student_id' => $student->id,
                        ],
                        [
                            'score' => rand(60, 100),
                            'graded_by' => $admin->id,
                        ]
                    );
                }
            }
        }
    }

    private function seedSpp(Tenant $tenant, array $students, User $admin): void
    {
        $sppType = SppType::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'SPP-BLN'],
            [
                'name' => 'SPP Bulanan',
                'amount' => 500000,
                'frequency' => 'monthly',
                'description' => 'Sumbangan Pembinaan Pendidikan Bulanan',
            ]
        );

        $months = [
            ['period' => '2025-07', 'due' => '2025-07-15'],
            ['period' => '2025-08', 'due' => '2025-08-15'],
            ['period' => '2025-09', 'due' => '2025-09-15'],
            ['period' => '2025-10', 'due' => '2025-10-15'],
            ['period' => '2025-11', 'due' => '2025-11-15'],
            ['period' => '2025-12', 'due' => '2025-12-15'],
        ];

        $paymentCount = 0;

        foreach ($students as $si => $student) {
            foreach ($months as $mi => $month) {
                // Determine status: first 3 months mostly paid, later months more overdue
                $rand = rand(1, 100);
                if ($mi < 3) {
                    $status = $rand <= 80 ? PaymentStatus::PAID : ($rand <= 90 ? PaymentStatus::PARTIAL : PaymentStatus::OVERDUE);
                } elseif ($mi < 5) {
                    $status = $rand <= 50 ? PaymentStatus::PAID : ($rand <= 70 ? PaymentStatus::PARTIAL : PaymentStatus::OVERDUE);
                } else {
                    $status = $rand <= 30 ? PaymentStatus::PAID : ($rand <= 50 ? PaymentStatus::UNPAID : PaymentStatus::OVERDUE);
                }

                $bill = SppBill::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'student_id' => $student->id,
                        'spp_type_id' => $sppType->id,
                        'period' => $month['period'],
                    ],
                    [
                        'amount' => 500000,
                        'discount_amount' => 0,
                        'final_amount' => 500000,
                        'due_date' => $month['due'],
                        'status' => $status,
                    ]
                );

                // Create payment records for paid/partial bills (only some to keep it manageable)
                if (in_array($status, [PaymentStatus::PAID, PaymentStatus::PARTIAL]) && $paymentCount < 50) {
                    $payAmount = $status === PaymentStatus::PAID ? 500000 : rand(100000, 400000);
                    $payment = Payment::updateOrCreate(
                        [
                            'tenant_id' => $tenant->id,
                            'reference_number' => sprintf('PAY-%s-%04d-%02d', $month['period'], $si + 1, $mi + 1),
                        ],
                        [
                            'student_id' => $student->id,
                            'amount' => $payAmount,
                            'payment_date' => Carbon::parse($month['due'])->subDays(rand(0, 10)),
                            'method' => collect([PaymentMethod::CASH, PaymentMethod::TRANSFER])->random(),
                            'recorded_by' => $admin->id,
                        ]
                    );

                    PaymentBillAllocation::updateOrCreate(
                        [
                            'tenant_id' => $tenant->id,
                            'payment_id' => $payment->id,
                            'spp_bill_id' => $bill->id,
                        ],
                        [
                            'amount' => $payAmount,
                        ]
                    );

                    $paymentCount++;
                }
            }
        }
    }

    private function seedAnnouncements(Tenant $tenant, User $admin): void
    {
        $announcements = [
            [
                'title' => 'Selamat Datang di Tahun Ajaran 2025/2026',
                'content' => 'Selamat datang kembali di SMP Negeri 1 Demo. Semoga tahun ajaran ini penuh dengan prestasi dan keberhasilan.',
                'is_pinned' => true,
                'published_at' => Carbon::create(2025, 7, 14),
            ],
            [
                'title' => 'Jadwal Penilaian Tengah Semester (PTS) Ganjil',
                'content' => 'PTS semester ganjil akan dilaksanakan pada tanggal 6-11 Oktober 2025. Harap siswa mempersiapkan diri dengan baik.',
                'is_pinned' => false,
                'published_at' => Carbon::create(2025, 9, 20),
            ],
            [
                'title' => 'Pembayaran SPP Bulan September',
                'content' => 'Diingatkan kepada orang tua/wali siswa untuk melunasi SPP bulan September sebelum tanggal 15 September 2025.',
                'is_pinned' => false,
                'published_at' => Carbon::create(2025, 9, 1),
            ],
            [
                'title' => 'Kegiatan Class Meeting',
                'content' => 'Class meeting akan diadakan pada tanggal 15-17 Desember 2025 dengan berbagai lomba menarik antar kelas.',
                'is_pinned' => false,
                'published_at' => Carbon::create(2025, 11, 20),
            ],
            [
                'title' => 'Libur Semester Ganjil',
                'content' => 'Libur semester ganjil dimulai tanggal 22 Desember 2025 sampai 3 Januari 2026. Selamat berlibur!',
                'is_pinned' => false,
                'published_at' => Carbon::create(2025, 12, 15),
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::updateOrCreate(
                ['tenant_id' => $tenant->id, 'title' => $data['title']],
                [
                    'content' => $data['content'],
                    'author_id' => $admin->id,
                    'target_type' => 'all',
                    'is_pinned' => $data['is_pinned'],
                    'published_at' => $data['published_at'],
                ]
            );
        }
    }

    private function seedBooks(Tenant $tenant): void
    {
        $categories = [];
        foreach (['Pelajaran', 'Fiksi', 'Non-Fiksi', 'Referensi', 'Majalah'] as $cat) {
            $categories[$cat] = BookCategory::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $cat],
                ['description' => "Kategori buku {$cat}"]
            );
        }

        $books = [
            ['title' => 'Matematika SMP Kelas 7', 'author' => 'Tim Kemendikbud', 'cat' => 'Pelajaran', 'isbn' => '978-602-1234-01-1'],
            ['title' => 'Matematika SMP Kelas 8', 'author' => 'Tim Kemendikbud', 'cat' => 'Pelajaran', 'isbn' => '978-602-1234-02-8'],
            ['title' => 'Bahasa Indonesia SMP Kelas 7', 'author' => 'Tim Kemendikbud', 'cat' => 'Pelajaran', 'isbn' => '978-602-1234-03-5'],
            ['title' => 'IPA Terpadu SMP', 'author' => 'Tim Kemendikbud', 'cat' => 'Pelajaran', 'isbn' => '978-602-1234-04-2'],
            ['title' => 'Atlas Indonesia', 'author' => 'Penerbit Erlangga', 'cat' => 'Referensi', 'isbn' => '978-602-1234-05-9'],
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'cat' => 'Fiksi', 'isbn' => '978-979-1227-00-1'],
            ['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'cat' => 'Fiksi', 'isbn' => '978-979-3062-00-0'],
            ['title' => 'Ensiklopedia Sains', 'author' => 'National Geographic', 'cat' => 'Non-Fiksi', 'isbn' => '978-602-1234-08-0'],
            ['title' => 'Kamus Besar Bahasa Indonesia', 'author' => 'Kemendikbud', 'cat' => 'Referensi', 'isbn' => '978-602-1234-09-7'],
            ['title' => 'Sejarah Indonesia Modern', 'author' => 'M.C. Ricklefs', 'cat' => 'Non-Fiksi', 'isbn' => '978-602-1234-10-3'],
        ];

        foreach ($books as $book) {
            Book::updateOrCreate(
                ['tenant_id' => $tenant->id, 'isbn' => $book['isbn']],
                [
                    'title' => $book['title'],
                    'author' => $book['author'],
                    'publisher' => 'Penerbit Utama',
                    'year' => 2024,
                    'category_id' => $categories[$book['cat']]->id,
                    'stock' => rand(5, 20),
                    'available_stock' => rand(3, 15),
                    'location' => 'Rak ' . chr(65 + rand(0, 4)) . '-' . rand(1, 5),
                ]
            );
        }
    }

    private function seedAssets(Tenant $tenant): void
    {
        $categories = [];
        foreach (['Elektronik', 'Furnitur', 'Alat Olahraga', 'Laboratorium', 'Kantor'] as $cat) {
            $categories[$cat] = AssetCategory::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $cat],
                ['description' => "Kategori aset {$cat}"]
            );
        }

        $assets = [
            ['name' => 'Proyektor Epson EB-X51', 'cat' => 'Elektronik', 'qty' => 9, 'value' => 7500000, 'condition' => AssetCondition::GOOD],
            ['name' => 'Laptop HP 14s', 'cat' => 'Elektronik', 'qty' => 20, 'value' => 8000000, 'condition' => AssetCondition::GOOD],
            ['name' => 'Meja Siswa', 'cat' => 'Furnitur', 'qty' => 300, 'value' => 350000, 'condition' => AssetCondition::GOOD],
            ['name' => 'Bola Basket Molten', 'cat' => 'Alat Olahraga', 'qty' => 10, 'value' => 250000, 'condition' => AssetCondition::MINOR_DAMAGE],
            ['name' => 'Mikroskop Binokuler', 'cat' => 'Laboratorium', 'qty' => 15, 'value' => 3500000, 'condition' => AssetCondition::GOOD],
        ];

        foreach ($assets as $i => $asset) {
            Asset::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => sprintf('AST-%03d', $i + 1)],
                [
                    'name' => $asset['name'],
                    'category_id' => $categories[$asset['cat']]->id,
                    'condition' => $asset['condition'],
                    'location' => 'Ruang ' . $asset['cat'],
                    'quantity' => $asset['qty'],
                    'value' => $asset['value'],
                    'acquisition_date' => Carbon::create(2023, rand(1, 12), rand(1, 28)),
                ]
            );
        }
    }

    private function seedNotificationTemplates(Tenant $tenant): void
    {
        $templates = [
            [
                'key' => 'spp_reminder',
                'subject' => 'Pengingat Pembayaran SPP',
                'body' => 'Yth. Orang Tua/Wali {{student_name}}, SPP bulan {{period}} sebesar Rp {{amount}} belum dibayar. Jatuh tempo: {{due_date}}. Silakan lakukan pembayaran. Terima kasih.',
                'channel' => 'whatsapp',
            ],
            [
                'key' => 'absen_alfa',
                'subject' => 'Pemberitahuan Ketidakhadiran',
                'body' => 'Yth. Orang Tua/Wali {{student_name}}, kami informasikan bahwa putra/putri Anda tidak hadir (Alfa) pada tanggal {{date}} di {{subject}}. Mohon konfirmasi. Terima kasih.',
                'channel' => 'whatsapp',
            ],
            [
                'key' => 'rapor_ready',
                'subject' => 'Rapor Siap Diunduh',
                'body' => 'Yth. Orang Tua/Wali {{student_name}}, rapor semester {{semester}} tahun ajaran {{academic_year}} sudah tersedia. Silakan unduh melalui portal. Terima kasih.',
                'channel' => 'whatsapp',
            ],
            [
                'key' => 'ppdb_status',
                'subject' => 'Update Status PPDB',
                'body' => 'Yth. {{parent_name}}, status pendaftaran PPDB dengan nomor {{registration_number}} atas nama {{student_name}} adalah: {{status}}. Terima kasih.',
                'channel' => 'whatsapp',
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['tenant_id' => $tenant->id, 'key' => $template['key']],
                [
                    'subject' => $template['subject'],
                    'body' => $template['body'],
                    'is_active' => true,
                    'channel' => $template['channel'],
                ]
            );
        }
    }
}
