<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
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
use App\Models\Achievement;
use App\Models\AlumniProfile;
use App\Models\Announcement;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AttendanceSession;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookLoan;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\CurriculumSetting;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\GalleryAlbum;
use App\Models\GalleryItem;
use App\Models\GradeLevel;
use App\Models\Message;
use App\Models\NotificationTemplate;
use App\Models\Payment;
use App\Models\PaymentBillAllocation;
use App\Models\Plan;
use App\Models\Post;
use App\Models\PPDBRegistration;
use App\Models\PPDBWave;
use App\Models\SchoolEvent;
use App\Models\Semester;
use App\Models\SppBill;
use App\Models\SppDiscount;
use App\Models\SppType;
use App\Models\Student;
use App\Models\StudentExtracurricular;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\TeachingSchedule;
use App\Models\Tenant;
use App\Models\User;
use App\Services\RaporService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Seeds a complete demo school ("SMP Negeri 1 Demo") plus a few other schools
// for the SaaS dashboard. Every date is relative to the day the seeder runs,
// so the demo always looks current. All demo accounts use the password
// "password":
//   superadmin@edusaas.id  admin@smpn1demo.id  operator@smpn1demo.id
//   guru@smpn1demo.id      siswa@smpn1demo.id  ortu@smpn1demo.id
class DemoSeeder extends Seeder
{
    public const PASSWORD = 'password';

    private string $passwordHash;

    private int $startYear;

    private Tenant $tenant;

    private User $admin;

    private User $parentUser;

    private AcademicYear $academicYear;

    private AcademicYear $previousYear;

    private Semester $semester;

    /** @var array<string, Classroom> */
    private array $classrooms = [];

    /** @var array<string, Subject> */
    private array $subjects = [];

    /** @var array<string, Teacher> main teacher per subject code */
    private array $teachers = [];

    /** @var array<string, Teacher> second teacher (grade 9) per subject code */
    private array $secondTeachers = [];

    /** @var array<int, int> teacher id => user id */
    private array $teacherUsers = [];

    /** @var array<string, array<int, Student>> class name => students */
    private array $students = [];

    /** @var array<string, array<string, ClassroomSubject>> */
    private array $classroomSubjects = [];

    /** @var array<string, AssessmentType> */
    private array $assessmentTypes = [];

    private array $maleFirstNames = [
        'Ahmad', 'Muhammad', 'Rizky', 'Dimas', 'Andi', 'Budi', 'Cahyo', 'Dwi', 'Eko', 'Fajar',
        'Galih', 'Hadi', 'Irfan', 'Joko', 'Krisna', 'Lukman', 'Maulana', 'Naufal', 'Omar', 'Prasetyo',
        'Rafi', 'Satria', 'Teguh', 'Umar', 'Vino', 'Wahyu', 'Yoga', 'Zaki', 'Arif', 'Bagas',
        'Deni', 'Faisal', 'Gilang', 'Hendra', 'Ilham', 'Jefri', 'Kevin', 'Luthfi', 'Nanda', 'Putra',
        'Rangga', 'Surya', 'Taufik', 'Wira', 'Yusuf', 'Aditya', 'Bagus', 'Danu', 'Firman', 'Hanif',
    ];

    private array $femaleFirstNames = [
        'Siti', 'Nur', 'Dewi', 'Anisa', 'Putri', 'Rina', 'Sri', 'Wulan', 'Yuni', 'Zahra',
        'Ayu', 'Bunga', 'Citra', 'Dian', 'Eka', 'Fitri', 'Gita', 'Hana', 'Indah', 'Jasmine',
        'Kartika', 'Lestari', 'Mega', 'Nadia', 'Oktavia', 'Pratiwi', 'Ratna', 'Sari', 'Tika', 'Ulfa',
        'Vera', 'Widya', 'Yanti', 'Amelia', 'Bella', 'Cantika', 'Della', 'Eva', 'Farah', 'Gina',
        'Halimah', 'Intan', 'Julia', 'Kirana', 'Laila', 'Nabila', 'Olivia', 'Puspita', 'Rahma', 'Salsa',
    ];

    private array $lastNames = [
        'Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Hidayat', 'Nugraha', 'Santoso', 'Wibowo', 'Setiawan', 'Purnama',
        'Permana', 'Ramadhan', 'Firmansyah', 'Kurniawan', 'Utama', 'Mahendra', 'Gunawan', 'Suryani', 'Handayani', 'Lestari',
        'Rahayu', 'Anggraini', 'Puspitasari', 'Fitriani', 'Wahyuni', 'Hartono', 'Susanto', 'Budiman', 'Halim', 'Iskandar',
        'Mulyadi', 'Hakim', 'Fauzi', 'Syahputra', 'Siregar', 'Nasution', 'Simanjuntak', 'Tanjung', 'Harahap', 'Lubis',
    ];

    private array $cities = ['Jakarta Pusat', 'Jakarta Timur', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Utara', 'Bekasi', 'Tangerang', 'Depok', 'Bogor', 'Tangerang Selatan'];

    public function run(): void
    {
        mt_srand(20260716);

        $this->passwordHash = Hash::make(self::PASSWORD);
        $this->startYear = now()->month >= 7 ? now()->year : now()->year - 1;

        $this->step('Plans & super admin');
        $plans = $this->seedPlans();
        $this->seedSuperAdmin();

        $this->step('Demo school');
        $this->tenant = $this->seedTenant($plans['professional']);
        Tenant::setCurrent($this->tenant);

        if (Student::exists()) {
            $this->command?->warn('The demo school already has data. Run "php artisan migrate:fresh --seed" to rebuild it.');
            Tenant::forgetCurrent();

            return;
        }

        $this->seedStaffAccounts();
        $this->step('Academic calendar, classes & subjects');
        $this->seedAcademicCalendar();
        $this->seedClassrooms();
        $this->seedSubjects();
        $this->step('Teachers');
        $this->seedTeachers();
        $this->step('Students & parents');
        $this->seedStudents();
        $this->seedParents();
        $this->step('Timetable');
        $this->seedTimetable();
        $this->step('Gradebook');
        $this->seedAssessments();
        $this->step('Attendance');
        $this->seedAttendance();
        $this->step('Tuition & payments');
        $this->seedTuition();
        $this->step('Report cards');
        $this->seedReportCards();
        $this->step('PPDB, announcements, library, assets, facilities, activities');
        $this->seedPpdb();
        $this->seedAnnouncements();
        $this->seedLibrary();
        $this->seedAssets();
        $this->seedFacilities();
        $this->seedExtracurriculars();
        $this->step('Alumni, messages & templates');
        $this->seedAlumni();
        $this->seedMessages();
        $this->seedNotificationTemplates();
        $this->step('School website content');
        $this->seedWebsite();

        Tenant::forgetCurrent();

        $this->step('Other schools (SaaS dashboard)');
        $this->seedOtherSchools($plans);

        $this->command?->info('✅ Demo data ready. Password for every demo account: ' . self::PASSWORD);
    }

    private function step(string $message): void
    {
        $this->command?->info('🌱 ' . $message);
    }

    private function user(string $email, string $name, UserType $type, ?string $phone = null, ?int $tenantId = null): User
    {
        return User::updateOrCreate(['email' => $email], [
            'tenant_id' => $tenantId ?? $this->tenant->id,
            'name' => $name,
            'password' => $this->passwordHash,
            'type' => $type,
            'phone' => $phone,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    /** @return array<int, Student> */
    private function allStudents(): array
    {
        return array_merge(...array_values($this->students));
    }

    private function seedPlans(): array
    {
        $plans = [
            'starter' => ['Starter', 500000, 5000000, 200, 20, ['attendance', 'grades', 'spp', 'announcements', 'portal'], 1],
            'professional' => ['Professional', 1000000, 10000000, 500, 50, ['attendance', 'grades', 'spp', 'announcements', 'portal', 'ppdb', 'library', 'assets', 'report_cards', 'whatsapp', 'website'], 2],
            'enterprise' => ['Enterprise', 2000000, 20000000, 2000, 200, ['attendance', 'grades', 'spp', 'announcements', 'portal', 'ppdb', 'library', 'assets', 'report_cards', 'whatsapp', 'website', 'api', 'custom_domain', 'priority_support'], 3],
        ];

        return collect($plans)->map(fn (array $p, string $slug) => Plan::updateOrCreate(['slug' => $slug], [
            'name' => $p[0],
            'price_monthly' => $p[1],
            'price_annual' => $p[2],
            'max_students' => $p[3],
            'max_teachers' => $p[4],
            'features' => $p[5],
            'is_active' => true,
            'sort_order' => $p[6],
        ]))->all();
    }

    private function seedSuperAdmin(): void
    {
        User::updateOrCreate(['email' => env('SUPER_ADMIN_EMAIL', 'superadmin@edusaas.id')], [
            'tenant_id' => null,
            'name' => 'Super Admin',
            'password' => env('SUPER_ADMIN_PASSWORD') ? Hash::make(env('SUPER_ADMIN_PASSWORD')) : $this->passwordHash,
            'type' => UserType::SUPER_ADMIN,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function seedTenant(Plan $plan): Tenant
    {
        $tenant = Tenant::updateOrCreate(['slug' => 'demo'], [
            'name' => 'SMP Negeri 1 Demo',
            'phone' => '(021) 555-1234',
            'email' => 'info@smpn1demo.id',
            'address' => 'Jl. Pendidikan No. 1, Cempaka Putih',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'npsn' => '20100001',
            'school_type' => SchoolType::SMP,
            'principal_name' => 'Dr. Bambang Suryadi, M.Pd.',
            'vision' => 'Terwujudnya peserta didik yang beriman, berprestasi, berkarakter Pancasila, dan peduli lingkungan.',
            'mission' => "Menyelenggarakan pembelajaran aktif, kreatif, dan menyenangkan.\nMengembangkan potensi akademik dan non-akademik setiap peserta didik.\nMenumbuhkan budaya literasi, numerasi, dan digital.\nMembentuk karakter religius, disiplin, dan gotong royong.\nMewujudkan sekolah yang bersih, hijau, dan ramah anak.",
            'description' => 'SMP Negeri 1 Demo adalah sekolah menengah pertama negeri terakreditasi A yang berdiri sejak 1965. Dengan 27 rombongan belajar, laboratorium lengkap, dan program literasi digital, sekolah kami telah melahirkan ribuan alumni yang berprestasi di tingkat regional maupun nasional.',
            'accreditation' => 'A',
            'founded_year' => 1965,
            'website' => 'https://smpn1demo.sch.id',
            'social_links' => [
                'instagram' => 'https://instagram.com/smpn1demo',
                'youtube' => 'https://youtube.com/@smpn1demo',
                'facebook' => 'https://facebook.com/smpn1demo',
            ],
            'status' => TenantStatus::ACTIVE,
            'currency' => 'IDR',
            'settings' => [
                'color_primary' => '#1e40af',
                'color_secondary' => '#f59e0b',
                'late_threshold_minutes' => 15,
                'principal_nip' => '196805121993031004',
            ],
        ]);

        $subscription = Subscription::updateOrCreate(['tenant_id' => $tenant->id, 'plan_id' => $plan->id], [
            'starts_at' => now()->subMonths(3)->startOfMonth(),
            'ends_at' => now()->addMonths(9)->endOfMonth(),
            'status' => 'active',
            'payment_method' => 'transfer',
            'payment_amount' => $plan->price_annual,
            'billing_cycle' => 'annual',
            'auto_renew' => true,
        ]);

        $tenant->update(['subscription_id' => $subscription->id]);

        return $tenant;
    }

    private function seedStaffAccounts(): void
    {
        $this->admin = $this->user('admin@smpn1demo.id', 'Admin SMP Negeri 1 Demo', UserType::SCHOOL_ADMIN, '081100000001');
        $this->user('operator@smpn1demo.id', 'Operator Tata Usaha', UserType::OPERATOR, '081100000002');
    }

    private function seedAcademicCalendar(): void
    {
        $y = $this->startYear;

        $this->previousYear = AcademicYear::create([
            'name' => ($y - 1) . '/' . $y,
            'starts_at' => Carbon::create($y - 1, 7, 15),
            'ends_at' => Carbon::create($y, 6, 21),
            'is_active' => false,
        ]);

        $this->academicYear = AcademicYear::create([
            'name' => $y . '/' . ($y + 1),
            'starts_at' => Carbon::create($y, 7, 13),
            'ends_at' => Carbon::create($y + 1, 6, 20),
            'is_active' => true,
        ]);

        $oddActive = now()->month >= 7;

        $odd = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Ganjil',
            'starts_at' => Carbon::create($y, 7, 13),
            'ends_at' => Carbon::create($y, 12, 19),
            'is_active' => $oddActive,
        ]);

        $even = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Genap',
            'starts_at' => Carbon::create($y + 1, 1, 5),
            'ends_at' => Carbon::create($y + 1, 6, 20),
            'is_active' => ! $oddActive,
        ]);

        $this->semester = $oddActive ? $odd : $even;

        CurriculumSetting::create([
            'academic_year_id' => $this->academicYear->id,
            'assessment_weights' => ['TGS' => 20, 'UH' => 30, 'PTS' => 20, 'PAS' => 30],
            'kkm_default' => 75,
            'passing_grade' => 75,
            'grading_type' => 'numeric',
        ]);
    }

    private function seedClassrooms(): void
    {
        foreach ([7, 8, 9] as $level) {
            $grade = GradeLevel::create(['name' => "Kelas {$level}", 'level' => $level, 'sort_order' => $level]);

            foreach (['A', 'B', 'C'] as $suffix) {
                $name = $level . $suffix;
                $this->classrooms[$name] = Classroom::create([
                    'grade_id' => $grade->id,
                    'academic_year_id' => $this->academicYear->id,
                    'name' => $name,
                    'capacity' => 32,
                    'room_name' => 'Ruang ' . $name,
                ]);
            }
        }
    }

    private function seedSubjects(): void
    {
        $subjects = [
            ['MTK', 'Matematika', SubjectType::TEORI, '#2563eb'],
            ['BIN', 'Bahasa Indonesia', SubjectType::TEORI, '#dc2626'],
            ['BIG', 'Bahasa Inggris', SubjectType::TEORI, '#7c3aed'],
            ['IPA', 'Ilmu Pengetahuan Alam', SubjectType::TEORI, '#059669'],
            ['IPS', 'Ilmu Pengetahuan Sosial', SubjectType::TEORI, '#d97706'],
            ['PKN', 'Pendidikan Pancasila', SubjectType::TEORI, '#b91c1c'],
            ['PAI', 'Pendidikan Agama dan Budi Pekerti', SubjectType::TEORI, '#15803d'],
            ['SBK', 'Seni Budaya', SubjectType::PRAKTEK, '#db2777'],
            ['PJK', 'PJOK', SubjectType::PRAKTEK, '#0891b2'],
            ['PKY', 'Prakarya', SubjectType::PRAKTEK, '#65a30d'],
            ['TIK', 'Informatika', SubjectType::PRAKTEK, '#4f46e5'],
            ['BDA', 'Bahasa Daerah', SubjectType::MUATAN_LOKAL, '#a16207'],
        ];

        foreach ($subjects as [$code, $name, $type, $color]) {
            $this->subjects[$code] = Subject::create(['code' => $code, 'name' => $name, 'type' => $type, 'color' => $color]);
        }
    }

    private function seedTeachers(): void
    {
        $rows = [
            ['Hadi Santoso', Gender::MALE, 'MTK', 'S.Pd.'], ['Siti Rahmawati', Gender::FEMALE, 'BIN', 'S.Pd.'],
            ['Ahmad Fauzi', Gender::MALE, 'BIG', 'S.Pd., M.Pd.'], ['Dewi Lestari', Gender::FEMALE, 'IPA', 'S.Si.'],
            ['Budi Prasetyo', Gender::MALE, 'IPS', 'S.Pd.'], ['Nur Hidayah', Gender::FEMALE, 'PKN', 'S.Pd.'],
            ['Muhammad Rizki', Gender::MALE, 'PAI', 'S.Pd.I.'], ['Rina Kartika', Gender::FEMALE, 'SBK', 'S.Sn.'],
            ['Eko Widodo', Gender::MALE, 'PJK', 'S.Pd.'], ['Anisa Putri', Gender::FEMALE, 'PKY', 'S.Pd.'],
            ['Surya Darma', Gender::MALE, 'TIK', 'S.Kom.'], ['Wulan Sari', Gender::FEMALE, 'BDA', 'S.Pd.'],
            ['Agus Salim', Gender::MALE, 'MTK', 'M.Pd.'], ['Ratna Dewi', Gender::FEMALE, 'BIN', 'S.Pd.'],
            ['Yohanes Sitompul', Gender::MALE, 'BIG', 'S.Pd.'], ['Fitri Handayani', Gender::FEMALE, 'IPA', 'S.Pd.'],
            ['Arif Rahman', Gender::MALE, 'IPS', 'S.Pd.'], ['Maria Theresia', Gender::FEMALE, 'PAI', 'S.Ag.'],
            ['Wahyu Nugroho', Gender::MALE, 'PJK', 'S.Or.'], ['Citra Ayuningtyas', Gender::FEMALE, 'BK', 'S.Psi.'],
        ];

        $classNames = array_keys($this->classrooms);

        foreach ($rows as $i => [$name, $gender, $code, $title]) {
            $email = $i === 0 ? 'guru@smpn1demo.id' : Str::slug($name, '.') . '@smpn1demo.id';
            $user = $this->user($email, $name, UserType::TEACHER, sprintf('0812%08d', 31000000 + $i));
            $homeroom = $i < 9 ? $this->classrooms[$classNames[$i]] : null;

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'nip' => sprintf('19%02d%02d%02d20%02d%02d1%03d', 75 + ($i % 20), ($i % 12) + 1, ($i % 27) + 1, 5 + ($i % 15), ($i % 12) + 1, $i + 1),
                'nuptk' => sprintf('%016d', 3150770000000000 + $i * 13),
                'full_name' => "{$name}, {$title}",
                'gender' => $gender,
                'birth_place' => $this->cities[$i % count($this->cities)],
                'birth_date' => Carbon::create(1975 + ($i % 20), ($i % 12) + 1, ($i % 27) + 1),
                'religion' => in_array($i, [14, 17], true) ? Religion::KRISTEN : Religion::ISLAM,
                'employment_status' => [EmploymentStatus::PNS, EmploymentStatus::PNS, EmploymentStatus::GTY, EmploymentStatus::GTT, EmploymentStatus::HONORER][$i % 5],
                'grade_group' => $i % 5 < 2 ? 'III/c' : null,
                'position' => $homeroom ? 'Wali Kelas ' . $homeroom->name : ($code === 'BK' ? 'Guru Bimbingan Konseling' : 'Guru Mata Pelajaran'),
                'education' => str_contains($title, 'M.') ? 'S2' : 'S1',
                'major' => $code === 'BK' ? 'Bimbingan dan Konseling' : 'Pendidikan ' . ($this->subjects[$code]->name ?? ''),
                'phone' => $user->phone,
                'email' => $email,
                'joined_at' => Carbon::create(2006 + ($i % 16), 7, 1),
                'is_homeroom_teacher' => (bool) $homeroom,
                'homeroom_classroom_id' => $homeroom?->id,
            ]);

            $homeroom?->update(['homeroom_teacher_id' => $teacher->id]);
            $this->teacherUsers[$teacher->id] = $user->id;

            if (isset($this->subjects[$code])) {
                if (isset($this->teachers[$code])) {
                    $this->secondTeachers[$code] = $teacher;
                } else {
                    $this->teachers[$code] = $teacher;
                }
            }
        }
    }

    // Grade 9 classes are taught by the second teacher of a subject when there is one.
    private function teacherFor(string $code, string $className): Teacher
    {
        return str_starts_with($className, '9') && isset($this->secondTeachers[$code])
            ? $this->secondTeachers[$code]
            : $this->teachers[$code];
    }

    private function seedStudents(): void
    {
        $perClass = [17, 17, 17, 17, 17, 17, 16, 16, 16];
        $religions = [Religion::ISLAM, Religion::ISLAM, Religion::ISLAM, Religion::ISLAM, Religion::ISLAM, Religion::ISLAM, Religion::ISLAM, Religion::KRISTEN, Religion::KATOLIK, Religion::HINDU];
        $index = 0;

        foreach (array_keys($this->classrooms) as $c => $className) {
            $level = (int) $className[0];
            $entryYear = $this->startYear - ($level - 7);

            for ($j = 0; $j < $perClass[$c]; $j++, $index++) {
                $male = $index % 2 === 0;
                $first = $male ? $this->maleFirstNames[$index % 50] : $this->femaleFirstNames[$index % 50];
                // The first two students are siblings (one parent account follows both).
                $last = $this->lastNames[$index === 1 ? 0 : $index % count($this->lastNames)];
                $nis = sprintf('%02d%04d', $entryYear % 100, $index + 1);
                $email = $index === 0 ? 'siswa@smpn1demo.id' : $nis . '@siswa.smpn1demo.id';
                $city = $this->cities[$index % count($this->cities)];

                $user = $this->user($email, "{$first} {$last}", UserType::STUDENT);

                $this->students[$className][] = Student::create([
                    'user_id' => $user->id,
                    'nis' => $nis,
                    'nisn' => sprintf('%010d', 1234500000 + $index * 7),
                    'classroom_id' => $this->classrooms[$className]->id,
                    'academic_year_id' => $this->academicYear->id,
                    'full_name' => "{$first} {$last}",
                    'nickname' => $first,
                    'gender' => $male ? Gender::MALE : Gender::FEMALE,
                    'birth_place' => $city,
                    'birth_date' => Carbon::create($this->startYear - 12 - ($level - 7), ($index % 12) + 1, ($index % 27) + 1),
                    'religion' => $religions[$index % 10],
                    'address' => 'Jl. Melati No. ' . ($index + 3) . ', ' . $city,
                    'city' => $city,
                    'province' => 'DKI Jakarta',
                    'phone' => sprintf('0857%08d', 20000000 + $index),
                    'email' => $email,
                    'blood_type' => ['A', 'B', 'O', 'AB'][$index % 4],
                    'hobbies' => ['Membaca', 'Sepak bola', 'Menggambar', 'Musik', 'Coding'][$index % 5],
                    'previous_school' => 'SD Negeri ' . (($index % 20) + 1) . ' Jakarta',
                    'status' => StudentStatus::ACTIVE,
                    'entry_year' => $entryYear,
                ]);
            }
        }
    }

    private function seedParents(): void
    {
        $this->parentUser = $this->user('ortu@smpn1demo.id', 'Agus Pratama', UserType::PARENT, '081298765432');

        $rows = [];
        $now = now();
        $jobs = ['PNS', 'Wiraswasta', 'Karyawan Swasta', 'Pedagang', 'TNI/Polri', 'Dokter', 'Guru', 'Petani'];
        $motherJobs = ['Ibu Rumah Tangga', 'PNS', 'Guru', 'Wiraswasta', 'Karyawan Swasta', 'Perawat'];
        $education = ['SMA', 'D3', 'S1', 'S2'];

        foreach ($this->allStudents() as $i => $student) {
            $demoFamily = $i <= 1;
            $lastName = Str::afterLast($student->full_name, ' ');

            foreach (['ayah', 'ibu'] as $relation) {
                $isFather = $relation === 'ayah';
                $rows[] = [
                    'tenant_id' => $this->tenant->id,
                    'student_id' => $student->id,
                    'relation' => $relation,
                    'name' => $isFather
                        ? ($demoFamily ? 'Agus Pratama' : $this->maleFirstNames[($i * 3) % 50] . ' ' . $lastName)
                        : ($demoFamily ? 'Sri Wahyuni' : $this->femaleFirstNames[($i * 7) % 50] . ' ' . $this->lastNames[($i * 5) % count($this->lastNames)]),
                    'nik' => sprintf('3171%012d', ($isFather ? 100000 : 500000) + $i),
                    'birth_date' => Carbon::create(1978 + ($i % 10), ($i % 12) + 1, ($i % 27) + 1)->toDateString(),
                    'education' => $education[$i % 4],
                    'occupation' => $isFather ? $jobs[$i % count($jobs)] : $motherJobs[$i % count($motherJobs)],
                    'income' => $isFather ? '5.000.000 - 10.000.000' : null,
                    'phone' => $demoFamily && $isFather ? '081298765432' : sprintf($isFather ? '0813%08d' : '0878%08d', 40000000 + $i),
                    'email' => $demoFamily && $isFather ? 'ortu@smpn1demo.id' : ($isFather ? 'ayah.' : 'ibu.') . $student->nis . '@smpn1demo.id',
                    'address' => $student->address,
                    'is_emergency_contact' => $isFather,
                    'is_whatsapp_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('student_parents')->insert($chunk);
        }
    }

    // 16 lessons per class over 20 weekly slots (4 per day, Mon–Fri). Each
    // class's layout is rotated by its index, and double-lesson subjects are
    // 10 slots apart, so no teacher is ever scheduled in two classes at once.
    private function seedTimetable(): void
    {
        $times = [['07:00', '08:20'], ['08:20', '09:40'], ['10:00', '11:20'], ['11:20', '12:40']];
        $layout = [0 => 'MTK', 10 => 'MTK', 1 => 'BIN', 11 => 'BIN', 2 => 'BIG', 12 => 'BIG', 3 => 'IPA', 13 => 'IPA',
            4 => 'IPS', 5 => 'PKN', 6 => 'PAI', 7 => 'SBK', 8 => 'PJK', 9 => 'PKY', 14 => 'TIK', 15 => 'BDA'];

        $c = 0;
        foreach ($this->classrooms as $className => $classroom) {
            foreach ($this->subjects as $code => $subject) {
                $this->classroomSubjects[$className][$code] = ClassroomSubject::create([
                    'classroom_id' => $classroom->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $this->teacherFor($code, $className)->id,
                    'hours_per_week' => in_array($code, ['MTK', 'BIN', 'BIG', 'IPA'], true) ? 4 : 2,
                    'academic_year_id' => $this->academicYear->id,
                    'semester_id' => $this->semester->id,
                ]);
            }

            foreach ($layout as $k => $code) {
                $slot = ($k + $c) % 20;
                $cs = $this->classroomSubjects[$className][$code];

                TeachingSchedule::create([
                    'teacher_id' => $cs->teacher_id,
                    'classroom_subject_id' => $cs->id,
                    'day_of_week' => intdiv($slot, 4) + 1,
                    'start_time' => $times[$slot % 4][0],
                    'end_time' => $times[$slot % 4][1],
                    'room' => $classroom->room_name,
                    'semester_id' => $this->semester->id,
                    'is_active' => true,
                ]);
            }

            $c++;
        }
    }

    // Class 7A has a full gradebook (every subject); the other classes have
    // Mathematics grades so the demo teacher sees data in all their classes.
    private function seedAssessments(): void
    {
        foreach ([['Tugas', 'TGS', 20, true], ['Ulangan Harian', 'UH', 30, true], ['Penilaian Tengah Semester', 'PTS', 20, true], ['Penilaian Akhir Semester', 'PAS', 30, true], ['Proyek P5', 'PRY', 0, false]] as [$name, $code, $weight, $final]) {
            $this->assessmentTypes[$code] = AssessmentType::create(['name' => $name, 'code' => $code, 'default_weight' => $weight, 'count_for_final' => $final]);
        }

        $start = $this->semester->starts_at->copy();
        $span = max(12, (int) abs($start->diffInDays(now())));
        $dateAt = fn (float $fraction) => $start->copy()->addDays((int) round($span * $fraction))->min(now()->subDay());
        $plan = [['TGS', 'Tugas 1', 0.2], ['UH', 'Ulangan Harian 1', 0.45], ['TGS', 'Tugas 2', 0.7], ['PTS', 'Penilaian Tengah Semester', 0.92]];

        $rows = [];
        $now = now();

        foreach ($this->classroomSubjects as $className => $bySubject) {
            foreach ($bySubject as $code => $cs) {
                $full = $className === '7A';

                if (! $full && $code !== 'MTK') {
                    continue;
                }

                foreach ($full ? $plan : array_slice($plan, 0, 2) as [$typeCode, $name, $fraction]) {
                    $assessment = Assessment::create([
                        'classroom_subject_id' => $cs->id,
                        'assessment_type_id' => $this->assessmentTypes[$typeCode]->id,
                        'semester_id' => $this->semester->id,
                        'name' => $name,
                        'date' => $dateAt($fraction),
                        'max_score' => 100,
                    ]);

                    foreach ($this->students[$className] as $student) {
                        $ability = 68 + (($student->id * 37) % 25);
                        $score = max(45, min(100, $ability + mt_rand(-12, 10)));
                        $remedial = $score < 75 && mt_rand(1, 100) <= 40;

                        $rows[] = [
                            'tenant_id' => $this->tenant->id,
                            'assessment_id' => $assessment->id,
                            'student_id' => $student->id,
                            'score' => $score,
                            'is_remedial' => $remedial,
                            'remedial_score' => $remedial ? 75 + mt_rand(0, 5) : null,
                            'notes' => null,
                            'graded_by' => $this->teacherUsers[$cs->teacher_id],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        foreach (array_chunk($rows, 300) as $chunk) {
            DB::table('student_grades')->insert($chunk);
        }
    }

    private function seedAttendance(): void
    {
        $from = $this->semester->starts_at->copy()->max(now()->subDays(21))->startOfDay();
        $dates = [];
        for ($day = $from->copy(); $day->lt(today()); $day->addDay()) {
            if ($day->isWeekday()) {
                $dates[] = $day->copy();
            }
        }

        $mathId = $this->subjects['MTK']->id;
        $schedules = TeachingSchedule::with('classroomSubject.classroom', 'classroomSubject.subject')->get()
            ->groupBy(fn (TeachingSchedule $s) => $s->classroomSubject->classroom->name);

        $rows = [];
        $now = now();

        foreach ($schedules as $className => $lessons) {
            $full = $className === '7A';

            foreach ($dates as $date) {
                if (! $full && $date->lt(now()->subDays(14))) {
                    continue;
                }

                foreach ($lessons->where('day_of_week', $date->dayOfWeekIso) as $lesson) {
                    if (! $full && $lesson->classroomSubject->subject_id !== $mathId) {
                        continue;
                    }

                    $start = $date->copy()->setTimeFromTimeString($lesson->start_time);

                    $session = AttendanceSession::create([
                        'classroom_subject_id' => $lesson->classroom_subject_id,
                        'teacher_id' => $lesson->teacher_id,
                        'date' => $date,
                        'start_time' => $lesson->start_time,
                        'end_time' => $lesson->end_time,
                        'topic' => 'Pembelajaran ' . $lesson->classroomSubject->subject->name,
                        'qr_generated_at' => $start,
                        'status' => 'closed',
                    ]);

                    foreach ($this->students[$className] as $student) {
                        $roll = mt_rand(1, 100);
                        $status = match (true) {
                            $roll <= 86 => 'hadir',
                            $roll <= 91 => 'terlambat',
                            $roll <= 95 => 'sakit',
                            $roll <= 98 => 'izin',
                            default => 'alfa',
                        };
                        $present = in_array($status, ['hadir', 'terlambat'], true);

                        $rows[] = [
                            'tenant_id' => $this->tenant->id,
                            'attendance_session_id' => $session->id,
                            'student_id' => $student->id,
                            'status' => $status,
                            'check_in_time' => $start->copy()->addMinutes($status === 'terlambat' ? mt_rand(16, 30) : mt_rand(0, 10)),
                            'method' => $present && mt_rand(0, 1) ? 'qr_code' : 'manual',
                            'notes' => $present ? null : ($status === 'sakit' ? 'Surat keterangan sakit' : null),
                            'notified_parent_at' => $status === 'alfa' ? $start->copy()->addHour() : null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        foreach (array_chunk($rows, 300) as $chunk) {
            DB::table('student_attendances')->insert($chunk);
        }

        // An open session right now, so the QR check-in can be tried live.
        $math7A = $this->classroomSubjects['7A']['MTK'];
        AttendanceSession::create([
            'classroom_subject_id' => $math7A->id,
            'teacher_id' => $math7A->teacher_id,
            'date' => today(),
            'start_time' => now()->subMinutes(5)->format('H:i'),
            'end_time' => now()->addMinutes(80)->format('H:i'),
            'topic' => 'Persamaan Linear Satu Variabel',
            'status' => 'open',
        ]);

        // Teacher check-ins for the last two weeks.
        $teacherRows = [];
        foreach (array_slice($dates, -10) as $date) {
            foreach ($this->teacherUsers as $teacherId => $userId) {
                $roll = mt_rand(1, 100);
                $status = $roll <= 92 ? 'hadir' : ($roll <= 96 ? 'izin' : 'sakit');
                $present = $status === 'hadir';

                $teacherRows[] = [
                    'tenant_id' => $this->tenant->id,
                    'teacher_id' => $teacherId,
                    'date' => $date->toDateString(),
                    'check_in_time' => $present ? $date->copy()->setTime(6, 30)->addMinutes(mt_rand(0, 35)) : null,
                    'check_out_time' => $present ? $date->copy()->setTime(14, 0)->addMinutes(mt_rand(0, 90)) : null,
                    'method' => $present ? (mt_rand(0, 1) ? 'fingerprint' : 'qr') : 'manual',
                    'location_lat' => $present ? -6.1754 + mt_rand(-50, 50) / 100000 : null,
                    'location_lng' => $present ? 106.8272 + mt_rand(-50, 50) / 100000 : null,
                    'status' => $status,
                    'notes' => $present ? null : 'Pengajuan ' . $status,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($teacherRows, 300) as $chunk) {
            DB::table('teacher_attendances')->insert($chunk);
        }
    }

    private function seedTuition(): void
    {
        $monthly = SppType::create(['name' => 'SPP Bulanan', 'code' => 'SPP-BLN', 'amount' => 350000, 'frequency' => 'monthly', 'applies_to' => 'all', 'description' => 'Sumbangan Pembinaan Pendidikan bulanan']);
        $activity = SppType::create(['name' => 'Dana Kegiatan Semester', 'code' => 'DKS', 'amount' => 250000, 'frequency' => 'semester', 'applies_to' => 'all', 'description' => 'Kegiatan OSIS, pramuka, dan kunjungan edukatif']);

        $scholars = [$this->students['7A'][5]->id, $this->students['8B'][2]->id];
        foreach ($scholars as $studentId) {
            SppDiscount::create([
                'student_id' => $studentId,
                'name' => 'Beasiswa Prestasi Akademik',
                'type' => 'percentage',
                'value' => 50,
                'valid_from' => $this->academicYear->starts_at,
                'valid_until' => $this->academicYear->ends_at,
                'notes' => 'Juara olimpiade sains tingkat kota',
            ]);
        }

        $months = [];
        for ($month = Carbon::create($this->startYear, 7, 1); $month->lte(now()->startOfMonth()->addMonth()) && count($months) < 12; $month->addMonth()) {
            $months[] = $month->copy();
        }

        $sequence = 0;

        foreach ($this->allStudents() as $i => $student) {
            $discount = in_array($student->id, $scholars, true) ? 0.5 : 0;

            foreach ($months as $month) {
                $amount = 350000;
                $final = $amount * (1 - $discount);
                $due = $month->copy()->day(10);
                $past = $month->lt(now()->startOfMonth());
                $current = $month->isSameMonth(now());
                $roll = mt_rand(1, 100);

                [$status, $paid] = match (true) {
                    $i === 0 && $past => [PaymentStatus::PAID, $final],
                    $i === 0 && $current => [PaymentStatus::PARTIAL, 150000],
                    $i === 0 => [PaymentStatus::UNPAID, 0],
                    $past => $roll <= 85 ? [PaymentStatus::PAID, $final] : ($roll <= 93 ? [PaymentStatus::PARTIAL, round($final / 2)] : [PaymentStatus::OVERDUE, 0]),
                    $current => $roll <= 55 ? [PaymentStatus::PAID, $final] : ($due->isPast() ? [PaymentStatus::OVERDUE, 0] : [PaymentStatus::UNPAID, 0]),
                    default => [PaymentStatus::UNPAID, 0],
                };

                $bill = SppBill::create([
                    'student_id' => $student->id,
                    'spp_type_id' => $monthly->id,
                    'period' => $month->format('Y-m'),
                    'amount' => $amount,
                    'discount_amount' => $amount - $final,
                    'final_amount' => $final,
                    'due_date' => $due,
                    'status' => $status,
                ]);

                if ($paid > 0) {
                    $this->recordPayment($student, $bill, $paid, $due->copy()->subDays(mt_rand(0, 8)), ++$sequence);
                }
            }

            $due = $this->semester->starts_at->copy()->addDays(30);
            $paidActivity = $i !== 0 && mt_rand(1, 100) <= 70;

            $bill = SppBill::create([
                'student_id' => $student->id,
                'spp_type_id' => $activity->id,
                'period' => $this->startYear . '-S' . ($this->semester->name === 'Ganjil' ? 1 : 2),
                'amount' => 250000,
                'discount_amount' => 0,
                'final_amount' => 250000,
                'due_date' => $due,
                'status' => $paidActivity ? PaymentStatus::PAID : ($due->isPast() ? PaymentStatus::OVERDUE : PaymentStatus::UNPAID),
            ]);

            if ($paidActivity) {
                $this->recordPayment($student, $bill, 250000, $due->copy()->subDays(mt_rand(1, 12)), ++$sequence);
            }
        }
    }

    private function recordPayment(Student $student, SppBill $bill, float $amount, Carbon $date, int $sequence): void
    {
        $date = $date->min(now());

        $payment = Payment::create([
            'student_id' => $student->id,
            'reference_number' => sprintf('KWT-%s-%05d', $date->format('Ym'), $sequence),
            'amount' => $amount,
            'payment_date' => $date,
            'method' => mt_rand(0, 2) ? PaymentMethod::TRANSFER : PaymentMethod::CASH,
            'recorded_by' => $this->admin->id,
        ]);

        PaymentBillAllocation::create([
            'payment_id' => $payment->id,
            'spp_bill_id' => $bill->id,
            'amount' => $amount,
        ]);
    }

    private function seedReportCards(): void
    {
        $rapor = app(RaporService::class);

        foreach ($this->students['7A'] as $student) {
            $card = $rapor->generateForStudent($student, $this->semester->id);
            $average = (float) $card->reportCardSubjects->avg('final_score');

            $comment = match (true) {
                $average >= 88 => "Ananda {$student->nickname} menunjukkan prestasi yang sangat baik dan menjadi teladan bagi teman-temannya. Pertahankan!",
                $average >= 80 => "Ananda {$student->nickname} aktif dan bertanggung jawab dalam belajar. Tingkatkan terus kemampuan di mata pelajaran eksakta.",
                default => "Ananda {$student->nickname} perlu lebih rajin mengulang pelajaran di rumah dan aktif bertanya di kelas.",
            };

            $card->update([
                'homeroom_comment' => $comment,
                'principal_comment' => 'Terus kembangkan karakter disiplin, jujur, dan gemar membaca.',
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ]);
        }
    }

    private function seedPpdb(): void
    {
        $y = $this->startYear;

        $nextYear = AcademicYear::create([
            'name' => ($y + 1) . '/' . ($y + 2),
            'starts_at' => Carbon::create($y + 1, 7, 13),
            'ends_at' => Carbon::create($y + 2, 6, 20),
            'is_active' => false,
        ]);

        $requirements = [
            'Ijazah / SKL SD' => 'Fotokopi dilegalisir',
            'Akta kelahiran' => 'Scan asli',
            'Kartu Keluarga' => 'Scan asli',
            'Pas foto 3x4' => 'Latar merah, 2 lembar',
            'Rapor kelas 4–6' => 'Fotokopi halaman nilai',
        ];

        $wave = PPDBWave::create([
            'academic_year_id' => $nextYear->id,
            'name' => 'Gelombang 1 — Jalur Zonasi & Prestasi',
            'quota_per_class' => 32,
            'opens_at' => now()->subDays(20)->startOfDay(),
            'closes_at' => now()->addDays(40)->endOfDay(),
            'requirements' => $requirements,
            'is_active' => true,
        ]);

        PPDBWave::create([
            'academic_year_id' => $nextYear->id,
            'name' => 'Gelombang 2 — Jalur Reguler',
            'quota_per_class' => 32,
            'opens_at' => now()->addDays(41)->startOfDay(),
            'closes_at' => now()->addDays(90)->endOfDay(),
            'requirements' => $requirements,
            'is_active' => true,
        ]);

        $statuses = [PPDBStatus::ACCEPTED, PPDBStatus::ACCEPTED, PPDBStatus::ACCEPTED, PPDBStatus::ACCEPTED, PPDBStatus::REJECTED,
            PPDBStatus::WAITLIST, PPDBStatus::WAITLIST, PPDBStatus::PENDING, PPDBStatus::PENDING, PPDBStatus::PENDING, PPDBStatus::PENDING, PPDBStatus::PENDING];

        foreach ($statuses as $i => $status) {
            $male = $i % 2 === 0;
            $registration = new PPDBRegistration([
                'ppdb_wave_id' => $wave->id,
                'registration_number' => sprintf('PPDB-%d-%d-%05d', now()->year, $this->tenant->id, $i + 1),
                'full_name' => ($male ? $this->maleFirstNames[30 + $i] : $this->femaleFirstNames[30 + $i]) . ' ' . $this->lastNames[20 + $i],
                'birth_date' => Carbon::create($y - 11, ($i % 12) + 1, ($i % 27) + 1),
                'gender' => $male ? Gender::MALE : Gender::FEMALE,
                'parent_name' => $this->maleFirstNames[($i * 7) % 50] . ' ' . $this->lastNames[20 + $i],
                'parent_phone' => sprintf('0812%08d', 70000000 + $i),
                'parent_email' => 'pendaftar' . ($i + 1) . '@contoh.id',
                'previous_school' => 'SD Negeri ' . ($i + 3) . ' Jakarta',
                'address' => 'Jl. Kenanga No. ' . ($i + 10) . ', Jakarta Pusat',
                'status' => $status,
                'notes' => $status === PPDBStatus::REJECTED ? 'Berkas tidak lengkap' : null,
                'reviewed_by' => $status === PPDBStatus::PENDING ? null : $this->admin->id,
                'reviewed_at' => $status === PPDBStatus::PENDING ? null : now()->subDays(3),
            ]);
            $registration->created_at = now()->subDays(19 - $i);
            $registration->save();
        }
    }

    private function seedAnnouncements(): void
    {
        $items = [
            ['Selamat Datang Tahun Ajaran ' . $this->academicYear->name, 'Selamat datang kembali di SMP Negeri 1 Demo. Semoga tahun ajaran ini penuh prestasi. Kegiatan belajar dimulai pukul 07.00 WIB.', 'all', null, true, 60],
            ['Jadwal Penilaian Tengah Semester', 'Penilaian Tengah Semester (PTS) dilaksanakan dua minggu lagi. Siswa diharapkan mempersiapkan diri dan membawa alat tulis lengkap.', 'students', null, false, 10],
            ['Rapat Orang Tua / Wali Siswa', 'Dimohon kehadiran Bapak/Ibu orang tua/wali pada rapat komite hari Sabtu pukul 09.00 WIB di Aula Serbaguna.', 'parents', null, true, 5],
            ['Pembayaran SPP Bulan ' . now()->translatedFormat('F'), 'Pembayaran SPP dapat dilakukan melalui portal orang tua (QRIS, virtual account, e-wallet) atau di loket tata usaha paling lambat tanggal 10.', 'parents', null, false, 12],
            ['Lomba Kebersihan Kelas', 'Kelas 7A mewakili sekolah dalam lomba kebersihan kelas tingkat kota. Mari jaga kebersihan ruang kelas bersama-sama.', 'specific_class', [(string) $this->classrooms['7A']->id], false, 3],
            ['Rapat Dewan Guru', 'Rapat evaluasi pembelajaran dilaksanakan hari Jumat pukul 13.30 WIB di ruang guru.', 'teachers', null, false, 2],
            ['Pendaftaran Ekstrakurikuler Dibuka', 'Pendaftaran ekstrakurikuler robotik, basket, paskibra, dan seni tari dibuka hingga akhir bulan ini melalui wali kelas.', 'all', null, false, 25],
        ];

        foreach ($items as [$title, $content, $target, $ids, $pinned, $daysAgo]) {
            Announcement::create([
                'title' => $title,
                'content' => '<p>' . e($content) . '</p>',
                'author_id' => $this->admin->id,
                'target_type' => $target,
                'target_ids' => $ids,
                'is_pinned' => $pinned,
                'published_at' => now()->subDays($daysAgo),
            ]);
        }
    }

    private function seedLibrary(): void
    {
        $categories = collect(['Buku Pelajaran', 'Fiksi', 'Non-Fiksi', 'Referensi', 'Majalah'])
            ->mapWithKeys(fn (string $name) => [$name => BookCategory::create(['name' => $name, 'description' => "Koleksi {$name}"])]);

        $books = [
            ['Matematika SMP/MTs Kelas VII', 'Tim Kemendikbudristek', 'Buku Pelajaran', '978-602-244-001-1'],
            ['Bahasa Indonesia SMP/MTs Kelas VII', 'Tim Kemendikbudristek', 'Buku Pelajaran', '978-602-244-002-8'],
            ['Ilmu Pengetahuan Alam SMP Kelas VIII', 'Tim Kemendikbudristek', 'Buku Pelajaran', '978-602-244-003-5'],
            ['English for Nusantara SMP Kelas VII', 'Tim Kemendikbudristek', 'Buku Pelajaran', '978-602-244-004-2'],
            ['Atlas Indonesia & Dunia', 'Penerbit Erlangga', 'Referensi', '978-602-298-005-9'],
            ['Laskar Pelangi', 'Andrea Hirata', 'Fiksi', '978-979-3062-79-2'],
            ['Bumi', 'Tere Liye', 'Fiksi', '978-602-03-3295-6'],
            ['Ensiklopedia Sains untuk Pelajar', 'National Geographic Kids', 'Non-Fiksi', '978-602-1234-08-0'],
            ['Kamus Besar Bahasa Indonesia', 'Badan Bahasa', 'Referensi', '978-602-1234-09-7'],
            ['Sejarah Indonesia Modern', 'M.C. Ricklefs', 'Non-Fiksi', '978-602-1234-10-3'],
            ['Majalah Bobo Edisi Sains', 'Kompas Gramedia', 'Majalah', '977-021-5566-00-1'],
            ['Filosofi Teras', 'Henry Manampiring', 'Non-Fiksi', '978-602-412-518-9'],
        ];

        $models = [];
        foreach ($books as $i => [$title, $author, $category, $isbn]) {
            $models[] = Book::create([
                'isbn' => $isbn,
                'title' => $title,
                'author' => $author,
                'publisher' => str_contains($author, 'Tim') ? 'Kemendikbudristek' : 'Gramedia Pustaka Utama',
                'year' => now()->year - ($i % 6),
                'category_id' => $categories[$category]->id,
                'stock' => 5 + ($i % 4) * 3,
                'available_stock' => 5 + ($i % 4) * 3,
                'location' => 'Rak ' . chr(65 + $i % 5) . '-' . (($i % 4) + 1),
                'description' => "Koleksi perpustakaan: {$title}.",
            ]);
        }

        $demo = $this->students['7A'][0];
        $loans = [
            [$models[5], $demo, -4, 3, null, 'borrowed', 0],
            [$models[1], $demo, -30, -23, -24, 'returned', 0],
            [$models[6], $this->students['7A'][3], -20, -13, null, 'overdue', 13000],
            [$models[0], $this->students['7A'][1], -6, 1, null, 'borrowed', 0],
            [$models[7], $this->students['8A'][2], -12, -5, -6, 'returned', 0],
            [$models[11], $this->students['9B'][4], -3, 4, null, 'borrowed', 0],
            [$models[9], $this->students['8C'][1], -15, -8, -2, 'returned', 6000],
            [$models[3], $this->teachers['BIG'], -10, 20, null, 'borrowed', 0],
        ];

        foreach ($loans as [$book, $borrower, $loanDays, $dueDays, $returnDays, $status, $fine]) {
            BookLoan::create([
                'book_id' => $book->id,
                'borrower_id' => $borrower->id,
                'borrower_type' => $borrower::class,
                'loan_date' => now()->addDays($loanDays),
                'due_date' => now()->addDays($dueDays),
                'return_date' => $returnDays !== null ? now()->addDays($returnDays) : null,
                'status' => $status,
                'fine_amount' => $fine,
                'fine_paid' => $fine > 0 && $returnDays !== null,
            ]);

            if ($returnDays === null) {
                $book->decrement('available_stock');
            }
        }
    }

    private function seedAssets(): void
    {
        $categories = collect(['Elektronik', 'Furnitur', 'Alat Olahraga', 'Laboratorium', 'Kantor'])
            ->mapWithKeys(fn (string $name) => [$name => AssetCategory::create(['name' => $name, 'description' => "Aset {$name}"])]);

        $assets = [
            ['Proyektor Epson EB-X51', 'Elektronik', 9, 7500000, AssetCondition::GOOD, 'Ruang kelas'],
            ['Laptop Lenovo IdeaPad Slim 3', 'Elektronik', 36, 8000000, AssetCondition::GOOD, 'Laboratorium Komputer'],
            ['Meja & Kursi Siswa', 'Furnitur', 300, 450000, AssetCondition::GOOD, 'Ruang kelas'],
            ['Bola Basket Molten GG7X', 'Alat Olahraga', 10, 650000, AssetCondition::MINOR_DAMAGE, 'Gudang olahraga'],
            ['Mikroskop Binokuler', 'Laboratorium', 15, 3500000, AssetCondition::GOOD, 'Laboratorium IPA'],
            ['Printer Epson L3210', 'Kantor', 4, 2500000, AssetCondition::MAJOR_DAMAGE, 'Ruang tata usaha'],
            ['Sound System Aula', 'Elektronik', 1, 18000000, AssetCondition::GOOD, 'Aula'],
        ];

        foreach ($assets as $i => [$name, $category, $qty, $value, $condition, $location]) {
            Asset::create([
                'code' => sprintf('AST-%s-%03d', $this->startYear, $i + 1),
                'name' => $name,
                'category_id' => $categories[$category]->id,
                'condition' => $condition,
                'location' => $location,
                'quantity' => $qty,
                'value' => $value,
                'acquisition_date' => Carbon::create($this->startYear - ($i % 4), ($i % 12) + 1, 15),
            ]);
        }
    }

    private function seedFacilities(): void
    {
        $facilities = collect([
            ['Aula Serbaguna', 'aula', 300, 'Gedung B lantai 1', 'available'],
            ['Lapangan Basket', 'lapangan', 100, 'Halaman belakang', 'available'],
            ['Laboratorium IPA', 'laboratorium', 36, 'Gedung C lantai 2', 'available'],
            ['Laboratorium Komputer', 'laboratorium', 36, 'Gedung C lantai 3', 'maintenance'],
            ['Perpustakaan', 'perpustakaan', 60, 'Gedung A lantai 2', 'available'],
            ['Ruang Musik', 'ruangan', 30, 'Gedung B lantai 2', 'available'],
        ])->map(fn (array $f) => Facility::create([
            'name' => $f[0], 'type' => $f[1], 'capacity' => $f[2], 'location' => $f[3], 'status' => $f[4],
            'description' => "{$f[0]} dengan kapasitas {$f[2]} orang.",
        ]));

        $bookings = [
            [0, $this->admin->id, 3, '09:00', '12:00', 'Rapat komite orang tua', 'approved'],
            [1, $this->teacherUsers[$this->teachers['PJK']->id], 1, '14:00', '16:00', 'Latihan ekstrakurikuler basket', 'approved'],
            [2, $this->teacherUsers[$this->teachers['IPA']->id], 2, '08:20', '09:40', 'Praktikum IPA kelas 8', 'approved'],
            [5, $this->teacherUsers[$this->teachers['SBK']->id], 4, '13:00', '15:00', 'Latihan paduan suara', 'pending'],
            [0, $this->admin->id, 10, '07:30', '11:00', 'Seminar literasi digital', 'pending'],
        ];

        foreach ($bookings as [$facility, $userId, $inDays, $start, $end, $purpose, $status]) {
            FacilityBooking::create([
                'facility_id' => $facilities[$facility]->id,
                'booked_by' => $userId,
                'date' => now()->addDays($inDays),
                'start_time' => $start,
                'end_time' => $end,
                'purpose' => $purpose,
                'status' => $status,
            ]);
        }
    }

    private function seedExtracurriculars(): void
    {
        $activities = [
            'pramuka' => Extracurricular::create(['name' => 'Pramuka', 'description' => 'Ekstrakurikuler wajib pembentukan karakter', 'teacher_id' => $this->teachers['PKN']->id, 'schedule' => 'Jumat, 14.00–16.00']),
            'basket' => Extracurricular::create(['name' => 'Basket', 'description' => 'Tim basket putra & putri', 'teacher_id' => $this->teachers['PJK']->id, 'schedule' => 'Selasa & Kamis, 14.00–16.00']),
            'robotik' => Extracurricular::create(['name' => 'Robotik & Coding', 'description' => 'Arduino, micro:bit, dan pemrograman', 'teacher_id' => $this->teachers['TIK']->id, 'schedule' => 'Rabu, 14.00–16.00']),
            'paskibra' => Extracurricular::create(['name' => 'Paskibra', 'description' => 'Pasukan pengibar bendera sekolah', 'teacher_id' => $this->teachers['PKN']->id, 'schedule' => 'Senin, 14.00–16.00']),
            'tari' => Extracurricular::create(['name' => 'Seni Tari', 'description' => 'Tari tradisional Nusantara', 'teacher_id' => $this->teachers['SBK']->id, 'schedule' => 'Kamis, 14.00–15.30']),
        ];

        $electives = ['basket', 'robotik', 'paskibra', 'tari'];
        $rows = [];
        $now = now();

        foreach (['7A', '7B', '8A'] as $className) {
            foreach ($this->students[$className] as $i => $student) {
                foreach (['pramuka', $electives[$i % 4]] as $key) {
                    $rows[] = [
                        'tenant_id' => $this->tenant->id,
                        'student_id' => $student->id,
                        'extracurricular_id' => $activities[$key]->id,
                        'academic_year_id' => $this->academicYear->id,
                        'score' => $i % 3 === 0 ? 'A' : 'B',
                        'description' => $i % 3 === 0 ? 'Sangat aktif dan menunjukkan kepemimpinan.' : 'Aktif mengikuti kegiatan.',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        DB::table('student_extracurriculars')->insert($rows);
    }

    private function seedAlumni(): void
    {
        $y = $this->startYear;
        $next = [['SMA Negeri 8 Jakarta', 'MIPA'], ['SMK Telkom Jakarta', 'Teknik Komputer & Jaringan'], ['MAN 4 Jakarta', 'IPS'], ['SMA Labschool Kebayoran', 'MIPA'], ['SMK Negeri 57 Jakarta', 'Tata Boga'], ['SMA Negeri 68 Jakarta', 'Bahasa']];
        $older = [['Universitas Indonesia', 'Kedokteran', 'Mahasiswa', null], ['Institut Teknologi Bandung', 'Teknik Informatika', 'Software Engineer', 'Gojek'], ['Universitas Gadjah Mada', 'Hukum', 'Advokat', 'Kantor Hukum Nusantara'], ['Politeknik Negeri Jakarta', 'Akuntansi', 'Staf Keuangan', 'Bank BTN'], ['Universitas Negeri Jakarta', 'Pendidikan Matematika', 'Guru', 'SMP Negeri 1 Demo'], ['Telkom University', 'Desain Komunikasi Visual', 'UI/UX Designer', 'PT Danum Inovasi Teknologi']];
        $testimonials = [
            'Guru-guru di SMP Negeri 1 Demo sangat peduli. Bekal disiplin dari sekolah sangat membantu saya di jenjang berikutnya.',
            'Ekstrakurikuler robotik membuat saya jatuh cinta pada teknologi. Sekarang saya bekerja sebagai software engineer.',
            'Lingkungan sekolah yang ramah dan penuh prestasi. Terima kasih atas semua kenangan indahnya!',
            'Program literasi membuat saya gemar membaca dan menulis hingga sekarang.',
            'Sekarang saya kembali ke sekolah ini sebagai guru. Bangga menjadi bagian keluarga besar SMP Negeri 1 Demo.',
            'Kegiatan pramuka dan paskibra melatih kepemimpinan yang saya pakai setiap hari di tempat kerja.',
        ];

        for ($i = 0; $i < 18; $i++) {
            $isRecent = $i < 12;
            $graduationYear = $isRecent ? $y : $y - 3 - ($i % 4);
            $male = $i % 2 === 1;
            $name = ($male ? $this->maleFirstNames[15 + $i] : $this->femaleFirstNames[15 + $i]) . ' ' . $this->lastNames[($i * 3) % count($this->lastNames)];

            $student = Student::create([
                'nis' => sprintf('%02d%04d', ($graduationYear - 3) % 100, 900 + $i),
                'nisn' => sprintf('%010d', 1122300000 + $i * 11),
                'classroom_id' => null,
                'academic_year_id' => $this->previousYear->id,
                'full_name' => $name,
                'nickname' => Str::before($name, ' '),
                'gender' => $male ? Gender::MALE : Gender::FEMALE,
                'birth_place' => $this->cities[$i % count($this->cities)],
                'birth_date' => Carbon::create($graduationYear - 15, ($i % 12) + 1, ($i % 27) + 1),
                'religion' => Religion::ISLAM,
                'status' => StudentStatus::ALUMNI,
                'entry_year' => $graduationYear - 3,
                'graduation_year' => $graduationYear,
            ]);

            [$school, $major, $job, $company] = $isRecent ? [...$next[$i % 6], 'Pelajar', null] : $older[$i % 6];

            AlumniProfile::create([
                'student_id' => $student->id,
                'alumni_number' => sprintf('ALM-%d-%03d', $graduationYear, $i + 1),
                'certificate_number' => sprintf('DN-01/D-SMP/%d/%07d', $graduationYear, 120000 + $i),
                'final_grade_average' => 80 + ($i % 15) + 0.5,
                'higher_education' => $school,
                'major' => $major,
                'current_occupation' => $job,
                'current_company' => $company,
                'current_city' => $this->cities[($i + 2) % count($this->cities)],
                'email' => Str::slug($name, '.') . '@alumni.smpn1demo.id',
                'testimonial' => $i % 3 === 0 ? $testimonials[intdiv($i, 3) % count($testimonials)] : null,
                'is_verified' => $i !== 5 && $i !== 11,
                'graduated_at' => Carbon::create($graduationYear, 6, 14),
            ]);
        }
    }

    private function seedMessages(): void
    {
        $demo = $this->students['7A'][0];
        $homeroomUser = $this->teacherUsers[$this->teachers['MTK']->id];
        $studentUser = $demo->user_id;

        $thread = (string) Str::uuid();
        $this->message($thread, $this->parentUser->id, $homeroomUser, $demo->id, 'Izin tidak masuk sekolah',
            "Selamat pagi Pak Hadi. Mohon izin, anak kami {$demo->nickname} hari ini tidak dapat masuk sekolah karena demam. Surat dokter akan kami kirimkan besok. Terima kasih.", now()->subDays(3), true);
        $this->message($thread, $homeroomUser, $this->parentUser->id, $demo->id, 'Izin tidak masuk sekolah',
            "Baik Pak Agus, terima kasih infonya. Semoga {$demo->nickname} lekas sembuh. Tugas hari ini akan saya titipkan melalui teman sekelasnya.", now()->subDays(3)->addHours(2), true);
        $this->message($thread, $this->parentUser->id, $homeroomUser, $demo->id, 'Izin tidak masuk sekolah',
            'Terima kasih banyak, Pak. Mohon info juga jadwal remedial matematika minggu ini.', now()->subHours(5), false);

        $thread = (string) Str::uuid();
        $this->message($thread, $studentUser, $homeroomUser, $demo->id, 'Pertanyaan tugas matematika',
            'Pak, untuk Tugas 2 apakah dikumpulkan dalam bentuk tulisan tangan atau boleh diketik?', now()->subDays(1), true);
        $this->message($thread, $homeroomUser, $studentUser, $demo->id, 'Pertanyaan tugas matematika',
            'Boleh keduanya. Pastikan langkah penyelesaiannya ditulis lengkap, ya.', now()->subHours(20), false);

        $thread = (string) Str::uuid();
        $this->message($thread, $this->admin->id, $homeroomUser, null, 'Input nilai PTS',
            'Bapak/Ibu wali kelas, mohon input nilai PTS paling lambat hari Jumat agar rapor tengah semester dapat diterbitkan.', now()->subDays(2), true);
    }

    private function message(string $thread, int $from, int $to, ?int $studentId, string $subject, string $content, Carbon $at, bool $read): void
    {
        $message = new Message([
            'thread_id' => $thread,
            'sender_id' => $from,
            'recipient_id' => $to,
            'student_id' => $studentId,
            'subject' => $subject,
            'content' => $content,
            'read_at' => $read ? $at->copy()->addMinutes(30) : null,
        ]);
        $message->created_at = $at;
        $message->updated_at = $at;
        $message->save();
    }

    // Website content: home page settings, news, agenda, achievements and
    // gallery. Cover images are generated SVG illustrations so the demo looks
    // complete without shipping photos.
    private function seedWebsite(): void
    {
        $this->tenant->update(['settings' => array_merge($this->tenant->settings ?? [], [
            'hero_title' => 'Sekolah Unggul, Berkarakter, dan Berprestasi',
            'hero_subtitle' => 'SMP Negeri 1 Demo membina generasi cerdas, kreatif, dan berakhlak mulia melalui pembelajaran modern dan kegiatan pengembangan diri yang beragam.',
            'principal_greeting' => "Assalamu'alaikum warahmatullahi wabarakatuh, salam sejahtera bagi kita semua.\n\nSelamat datang di website resmi SMP Negeri 1 Demo. Website ini kami hadirkan sebagai jendela informasi bagi siswa, orang tua, alumni, dan masyarakat. Melalui layanan digital seperti portal siswa, portal orang tua, dan PPDB online, kami berkomitmen menghadirkan layanan pendidikan yang transparan, cepat, dan mudah diakses.\n\nMari bersama-sama mewujudkan sekolah yang unggul dan berkarakter.",
            'history' => "SMP Negeri 1 Demo berdiri pada tahun 1965 dengan hanya 3 ruang kelas dan 90 siswa. Seiring perkembangan kota, sekolah terus tumbuh hingga kini memiliki 27 rombongan belajar, laboratorium IPA dan komputer, perpustakaan digital, serta lapangan olahraga.\n\nSekolah meraih akreditasi A sejak 2008 dan dikenal aktif dalam olimpiade sains, literasi, serta kegiatan seni dan olahraga di tingkat provinsi maupun nasional.",
            'whatsapp' => '081234567890',
            'office_hours' => 'Senin–Jumat, 07.00–15.00 WIB',
        ])]);

        $palette = [['#1E3A5F', '#2D5F8A'], ['#0F766E', '#14B8A6'], ['#7C2D12', '#EA580C'], ['#4C1D95', '#7C3AED'], ['#9D174D', '#DB2777'], ['#14532D', '#16A34A']];
        $cover = function (string $directory, string $title, int $i) use ($palette): string {
            [$from, $to] = $palette[$i % count($palette)];
            $lines = array_slice(explode("\n", wordwrap($title, 26, "\n", true)), 0, 3);
            $text = collect($lines)->map(fn ($line, $n) => '<text x="60" y="' . (270 + $n * 58) . '" font-family="Arial, sans-serif" font-size="46" font-weight="700" fill="#ffffff">' . e($line) . '</text>')->implode('');
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="675" viewBox="0 0 1200 675"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="' . $from . '"/><stop offset="1" stop-color="' . $to . '"/></linearGradient></defs><rect width="1200" height="675" fill="url(#g)"/><circle cx="1040" cy="120" r="220" fill="#ffffff" opacity="0.08"/><circle cx="1100" cy="600" r="160" fill="#F59E0B" opacity="0.25"/><text x="60" y="150" font-family="Arial, sans-serif" font-size="30" fill="#FBBF24" font-weight="700">SMP NEGERI 1 DEMO</text>' . $text . '</svg>';
            $path = $directory . '/' . Str::slug($title) . '-' . $i . '.svg';
            Storage::disk('public')->put($path, $svg);

            return $path;
        };

        $posts = [
            ['Tim Olimpiade Sains Raih Medali Emas Tingkat Provinsi', 'news', true, 3, 'Tiga siswa kelas 9 berhasil membawa pulang medali emas dan perak pada Olimpiade Sains Nasional tingkat provinsi.'],
            ['Pembukaan Pendaftaran Peserta Didik Baru Tahun Ajaran ' . ($this->startYear + 1) . '/' . ($this->startYear + 2), 'announcement', true, 20, 'PPDB online telah dibuka. Calon peserta didik dapat mendaftar, mengunggah berkas, dan memantau status secara daring.'],
            ['Gerakan Literasi Sekolah: 15 Menit Membaca Setiap Pagi', 'article', false, 9, 'Program literasi pagi meningkatkan minat baca siswa dan menghasilkan lebih dari 300 resensi buku dalam satu semester.'],
            ['Kunjungan Edukatif ke Museum Nasional Indonesia', 'news', false, 14, 'Siswa kelas 7 belajar sejarah secara langsung melalui kunjungan edukatif yang dipandu pemandu museum.'],
            ['Tips Belajar Efektif Menghadapi Penilaian Tengah Semester', 'article', false, 6, 'Guru BK membagikan strategi belajar, manajemen waktu, dan menjaga kesehatan menjelang penilaian.'],
            ['Tim Robotik Lolos ke Final Kompetisi Nasional', 'news', true, 25, 'Tim robotik sekolah melaju ke babak final setelah menampilkan robot pemilah sampah otomatis.'],
            ['Peringatan Hari Kemerdekaan dengan Lomba Tradisional', 'news', false, 35, 'Rangkaian lomba tradisional mempererat kebersamaan warga sekolah dalam memperingati HUT RI.'],
            ['Program Adiwiyata: Sekolah Hijau dan Bebas Sampah Plastik', 'article', false, 45, 'Bank sampah, kebun sekolah, dan larangan plastik sekali pakai menjadi bagian dari program Adiwiyata.'],
        ];

        foreach ($posts as $i => [$title, $category, $featured, $daysAgo, $excerpt]) {
            Post::create([
                'author_id' => $this->admin->id,
                'category' => $category,
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => '<p>' . e($excerpt) . '</p><p>Kegiatan ini merupakan bagian dari komitmen SMP Negeri 1 Demo dalam mengembangkan potensi akademik dan karakter peserta didik. Kepala sekolah menyampaikan apresiasi kepada seluruh guru, orang tua, dan siswa yang telah berpartisipasi.</p><h3>Rencana tindak lanjut</h3><ul><li>Pembinaan berkelanjutan melalui kegiatan ekstrakurikuler</li><li>Kolaborasi dengan komite sekolah dan orang tua</li><li>Publikasi hasil kegiatan melalui website dan media sosial sekolah</li></ul><p>Informasi lebih lanjut dapat diperoleh melalui tata usaha sekolah.</p>',
                'cover_image' => $cover('website/posts', $title, $i),
                'is_published' => true,
                'is_featured' => $featured,
                'published_at' => now()->subDays($daysAgo),
                'views' => 40 + $i * 37,
            ]);
        }

        $events = [
            ['Penilaian Tengah Semester', 'exam', 12, 5, 'Ruang kelas masing-masing'],
            ['Rapat Komite dan Orang Tua Siswa', 'meeting', 3, 0, 'Aula Serbaguna'],
            ['Class Meeting & Pentas Seni', 'academic', 30, 2, 'Lapangan & Aula'],
            ['Final Kompetisi Robotik Nasional', 'competition', 18, 1, 'Jakarta International Expo'],
            ['Libur Hari Besar Nasional', 'holiday', 22, 0, null],
            ['Seminar Literasi Digital untuk Orang Tua', 'meeting', 10, 0, 'Aula Serbaguna'],
            ['Upacara Hari Kemerdekaan', 'academic', -30, 0, 'Lapangan upacara'],
            ['Masa Pengenalan Lingkungan Sekolah', 'academic', -60, 2, 'SMP Negeri 1 Demo'],
        ];

        foreach ($events as [$title, $category, $inDays, $duration, $location]) {
            $start = now()->addDays($inDays)->setTime(8, 0);
            SchoolEvent::create([
                'title' => $title,
                'category' => $category,
                'location' => $location,
                'description' => "{$title} untuk seluruh warga sekolah. Informasi detail akan disampaikan melalui wali kelas.",
                'starts_at' => $start,
                'ends_at' => $start->copy()->addDays($duration)->setTime($duration ? 15 : 11, 0),
                'is_published' => true,
            ]);
        }

        $achievements = [
            ['Olimpiade Sains Nasional (OSN) Matematika', 'Medali Emas', 'province', 'academic', 20, 'Dinas Pendidikan Provinsi DKI Jakarta'],
            ['Kompetisi Robotik Pelajar Indonesia', 'Finalis', 'national', 'technology', 25, 'Kemendikbudristek'],
            ['Festival Tunas Bahasa Ibu', 'Juara 1', 'city', 'arts', 40, 'Suku Dinas Pendidikan Jakarta Pusat'],
            ['Kejuaraan Basket Pelajar', 'Juara 2', 'city', 'sports', 55, 'KONI Kota'],
            ['Lomba Karya Tulis Ilmiah Remaja', 'Juara 3', 'national', 'academic', 70, 'BRIN'],
            ['Musabaqah Tilawatil Quran Pelajar', 'Juara 1', 'district', 'religion', 80, 'Kementerian Agama'],
            ['Sekolah Adiwiyata', 'Penghargaan', 'province', 'other', 120, 'Dinas Lingkungan Hidup'],
            ['Olimpiade Informatika', 'Medali Perak', 'international', 'technology', 150, 'Asia Pacific Informatics Olympiad'],
        ];

        foreach ($achievements as $i => [$title, $rank, $level, $category, $daysAgo, $organizer]) {
            $student = $this->students[['9A', '9B', '8A', '8C', '9C', '7B', '7A', '9A'][$i]][$i % 5];
            $isSchool = $category === 'other';

            Achievement::create([
                'student_id' => $isSchool ? null : $student->id,
                'title' => $title,
                'participant' => $isSchool ? $this->tenant->name : $student->full_name,
                'rank' => $rank,
                'level' => $level,
                'category' => $category,
                'organizer' => $organizer,
                'achieved_at' => now()->subDays($daysAgo),
                'description' => "{$rank} pada {$title}.",
                'image' => $cover('website/achievements', $rank . ' ' . $title, $i + 2),
                'is_published' => true,
            ]);
        }

        $albums = [
            ['Masa Pengenalan Lingkungan Sekolah', 60, ['Upacara pembukaan', 'Perkenalan ekstrakurikuler', 'Games kebersamaan']],
            ['Peringatan Hari Kemerdekaan', 30, ['Upacara bendera', 'Lomba balap karung', 'Tarik tambang antar kelas']],
            ['Praktikum IPA Kelas 8', 15, ['Pengamatan mikroskop', 'Percobaan fotosintesis']],
            ['Profil Sekolah (Video)', 5, []],
        ];

        foreach ($albums as $i => [$title, $daysAgo, $photos]) {
            $album = GalleryAlbum::create([
                'title' => $title,
                'description' => "Dokumentasi kegiatan {$title}.",
                'cover_image' => $cover('website/gallery', $title, $i + 1),
                'event_date' => now()->subDays($daysAgo),
                'is_published' => true,
            ]);

            foreach ($photos as $n => $caption) {
                GalleryItem::create([
                    'gallery_album_id' => $album->id,
                    'type' => 'photo',
                    'image' => $cover('website/gallery', $caption, $i + $n + 3),
                    'caption' => $caption,
                    'sort_order' => $n,
                ]);
            }

            if ($photos === []) {
                GalleryItem::create([
                    'gallery_album_id' => $album->id,
                    'type' => 'video',
                    'video_url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
                    'caption' => 'Video profil sekolah',
                    'sort_order' => 0,
                ]);
            }
        }
    }

    private function seedNotificationTemplates(): void
    {
        $templates = [
            ['spp_reminder', 'Pengingat Pembayaran SPP', 'Yth. Orang Tua/Wali {{student_name}}, tagihan {{period}} sebesar {{amount}} jatuh tempo pada {{due_date}}. Pembayaran dapat dilakukan melalui portal orang tua. Terima kasih.'],
            ['absen_alfa', 'Pemberitahuan Ketidakhadiran', 'Yth. Orang Tua/Wali {{student_name}}, putra/putri Anda tercatat tidak hadir (alfa) pada {{date}} ({{subject}}). Mohon konfirmasi kepada wali kelas. Terima kasih.'],
            ['rapor_ready', 'Rapor Siap Diunduh', 'Yth. Orang Tua/Wali {{student_name}}, rapor semester {{semester}} tahun ajaran {{academic_year}} sudah terbit dan dapat diunduh melalui portal orang tua.'],
            ['ppdb_status', 'Update Status PPDB', 'Yth. {{parent_name}}, status pendaftaran PPDB nomor {{registration_number}} atas nama {{student_name}}: {{status}}. Terima kasih.'],
            ['payment_receipt', 'Pembayaran Diterima', 'Terima kasih, pembayaran {{amount}} untuk {{student_name}} telah kami terima pada {{date}} (No. {{reference}}).'],
        ];

        foreach ($templates as [$key, $subject, $body]) {
            NotificationTemplate::create(['key' => $key, 'subject' => $subject, 'body' => $body, 'channel' => 'whatsapp', 'is_active' => true]);
        }
    }

    private function seedOtherSchools(array $plans): void
    {
        $schools = [
            ['sd-harapan-bangsa', 'SD Harapan Bangsa', SchoolType::SD, 'Bandung', 'Jawa Barat', TenantStatus::TRIAL, 'starter', 'admin@sdharapanbangsa.sch.id', now()->subDays(9)],
            ['sma-nusantara', 'SMA Nusantara', SchoolType::SMA, 'Surabaya', 'Jawa Timur', TenantStatus::ACTIVE, 'enterprise', 'admin@smanusantara.sch.id', now()->subMonths(4)],
            ['mts-al-hikmah', 'MTs Al-Hikmah', SchoolType::MTS, 'Yogyakarta', 'DI Yogyakarta', TenantStatus::ACTIVE, 'professional', 'admin@mtsalhikmah.sch.id', now()->subMonths(2)],
        ];

        foreach ($schools as [$slug, $name, $type, $city, $province, $status, $planSlug, $email, $createdAt]) {
            $tenant = Tenant::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'school_type' => $type,
                'city' => $city,
                'province' => $province,
                'email' => 'info@' . Str::after($email, '@'),
                'phone' => '(022) 555-' . random_int(1000, 9999),
                'status' => $status,
                'currency' => 'IDR',
                'trial_ends_at' => $status === TenantStatus::TRIAL ? now()->addDays(5) : null,
            ]);
            $tenant->forceFill(['created_at' => $createdAt])->saveQuietly();

            $plan = $plans[$planSlug];
            $subscription = Subscription::updateOrCreate(['tenant_id' => $tenant->id, 'plan_id' => $plan->id], [
                'starts_at' => $createdAt,
                'ends_at' => $status === TenantStatus::TRIAL ? now()->addDays(5) : $createdAt->copy()->addYear(),
                'status' => 'active',
                'payment_method' => $status === TenantStatus::TRIAL ? 'trial' : 'midtrans',
                'payment_amount' => $status === TenantStatus::TRIAL ? 0 : $plan->price_annual,
                'billing_cycle' => 'annual',
                'auto_renew' => $status !== TenantStatus::TRIAL,
            ]);
            $tenant->update(['subscription_id' => $subscription->id]);

            $this->user($email, 'Admin ' . $name, UserType::SCHOOL_ADMIN, null, $tenant->id);
        }
    }
}
