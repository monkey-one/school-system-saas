<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by module
        $permissions = $this->getPermissions();

        // Create all permissions
        $allPermissions = [];
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                $allPermissions[] = Permission::firstOrCreate(
                    ['name' => $perm, 'guard_name' => 'web']
                );
            }
        }

        $this->command->info('Created ' . count($allPermissions) . ' permissions.');

        // Create roles and assign permissions
        $this->createSuperAdminRole($permissions);
        $this->createSchoolAdminRole($permissions);
        $this->createOperatorRole($permissions);
        $this->createTeacherRole($permissions);

        // Assign roles to existing users
        $this->assignRolesToExistingUsers();

        $this->command->info('Roles and permissions seeded successfully.');
    }

    private function getPermissions(): array
    {
        return [
            // SuperAdmin Panel Resources
            'user_management' => [
                'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user',
            ],
            'role_management' => [
                'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role',
            ],
            'permission_management' => [
                'view_any_permission', 'view_permission', 'create_permission', 'update_permission', 'delete_permission',
            ],
            'tenant_management' => [
                'view_any_tenant', 'view_tenant', 'create_tenant', 'update_tenant', 'delete_tenant',
            ],
            'subscription_management' => [
                'view_any_subscription', 'view_subscription', 'create_subscription', 'update_subscription', 'delete_subscription',
            ],
            'plan_management' => [
                'view_any_plan', 'view_plan', 'create_plan', 'update_plan', 'delete_plan',
            ],
            'activity_log' => [
                'view_any_activity_log', 'view_activity_log',
            ],

            // SchoolAdmin Panel Resources - Academic
            'academic_year' => [
                'view_any_academic_year', 'view_academic_year', 'create_academic_year', 'update_academic_year', 'delete_academic_year',
            ],
            'semester' => [
                'view_any_semester', 'view_semester', 'create_semester', 'update_semester', 'delete_semester',
            ],
            'grade_level' => [
                'view_any_grade_level', 'view_grade_level', 'create_grade_level', 'update_grade_level', 'delete_grade_level',
            ],
            'classroom' => [
                'view_any_classroom', 'view_classroom', 'create_classroom', 'update_classroom', 'delete_classroom',
            ],
            'subject' => [
                'view_any_subject', 'view_subject', 'create_subject', 'update_subject', 'delete_subject',
            ],
            'classroom_subject' => [
                'view_any_classroom_subject', 'view_classroom_subject', 'create_classroom_subject', 'update_classroom_subject', 'delete_classroom_subject',
            ],
            'curriculum_setting' => [
                'view_any_curriculum_setting', 'view_curriculum_setting', 'create_curriculum_setting', 'update_curriculum_setting', 'delete_curriculum_setting',
            ],

            // SchoolAdmin Panel Resources - Student Affairs
            'student' => [
                'view_any_student', 'view_student', 'create_student', 'update_student', 'delete_student',
            ],
            'ppdb_wave' => [
                'view_any_ppdb_wave', 'view_ppdb_wave', 'create_ppdb_wave', 'update_ppdb_wave', 'delete_ppdb_wave',
            ],
            'ppdb_registration' => [
                'view_any_ppdb_registration', 'view_ppdb_registration', 'create_ppdb_registration', 'update_ppdb_registration', 'delete_ppdb_registration',
            ],

            // SchoolAdmin Panel Resources - Staff
            'teacher' => [
                'view_any_teacher', 'view_teacher', 'create_teacher', 'update_teacher', 'delete_teacher',
            ],
            'teaching_schedule' => [
                'view_any_teaching_schedule', 'view_teaching_schedule', 'create_teaching_schedule', 'update_teaching_schedule', 'delete_teaching_schedule',
            ],

            // SchoolAdmin Panel Resources - Attendance
            'attendance_session' => [
                'view_any_attendance_session', 'view_attendance_session', 'create_attendance_session', 'update_attendance_session', 'delete_attendance_session',
            ],

            // SchoolAdmin Panel Resources - Grading
            'assessment' => [
                'view_any_assessment', 'view_assessment', 'create_assessment', 'update_assessment', 'delete_assessment',
            ],
            'assessment_type' => [
                'view_any_assessment_type', 'view_assessment_type', 'create_assessment_type', 'update_assessment_type', 'delete_assessment_type',
            ],

            // SchoolAdmin Panel Resources - Finance
            'payment' => [
                'view_any_payment', 'view_payment', 'create_payment', 'update_payment', 'delete_payment',
            ],
            'spp_bill' => [
                'view_any_spp_bill', 'view_spp_bill', 'create_spp_bill', 'update_spp_bill', 'delete_spp_bill',
            ],
            'spp_type' => [
                'view_any_spp_type', 'view_spp_type', 'create_spp_type', 'update_spp_type', 'delete_spp_type',
            ],

            // SchoolAdmin Panel Resources - Communication
            'announcement' => [
                'view_any_announcement', 'view_announcement', 'create_announcement', 'update_announcement', 'delete_announcement',
            ],

            // SchoolAdmin Panel Resources - Library
            'book' => [
                'view_any_book', 'view_book', 'create_book', 'update_book', 'delete_book',
            ],
            'book_loan' => [
                'view_any_book_loan', 'view_book_loan', 'create_book_loan', 'update_book_loan', 'delete_book_loan',
            ],

            // SchoolAdmin Panel Resources - Inventory
            'asset' => [
                'view_any_asset', 'view_asset', 'create_asset', 'update_asset', 'delete_asset',
            ],
            'facility' => [
                'view_any_facility', 'view_facility', 'create_facility', 'update_facility', 'delete_facility',
            ],
            'facility_booking' => [
                'view_any_facility_booking', 'view_facility_booking', 'create_facility_booking', 'update_facility_booking', 'delete_facility_booking',
            ],

            // Teacher Panel Resources
            'my_schedule' => [
                'view_any_my_schedule', 'view_my_schedule',
            ],
            'my_attendance_session' => [
                'view_any_my_attendance_session', 'view_my_attendance_session', 'create_my_attendance_session', 'update_my_attendance_session',
            ],
            'student_attendance' => [
                'view_any_student_attendance', 'view_student_attendance', 'create_student_attendance', 'update_student_attendance',
            ],
            'my_assessment' => [
                'view_any_my_assessment', 'view_my_assessment', 'create_my_assessment', 'update_my_assessment',
            ],
            'student_grade' => [
                'view_any_student_grade', 'view_student_grade', 'create_student_grade', 'update_student_grade',
            ],

            // Dashboard & Widgets
            'dashboard' => [
                'view_dashboard',
                'view_stats_overview_widget',
                'view_attendance_chart_widget',
                'view_spp_widget',
                'view_recent_activity_widget',
                'view_revenue_chart_widget',
            ],
        ];
    }

    private function createSuperAdminRole(array $permissions): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web']
        );

        // Super admin gets ALL permissions
        $allPerms = collect($permissions)->flatten()->all();
        $role->syncPermissions($allPerms);

        $this->command->info("Role 'super_admin' created with " . count($allPerms) . " permissions.");
    }

    private function createSchoolAdminRole(array $permissions): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'school_admin', 'guard_name' => 'web']
        );

        // School admin gets all school-level permissions (not super admin panel resources)
        $superAdminOnly = [
            'user_management', 'role_management', 'permission_management',
            'tenant_management', 'subscription_management', 'plan_management',
            'activity_log',
        ];

        $schoolPerms = collect($permissions)
            ->except($superAdminOnly)
            ->flatten()
            ->all();

        $role->syncPermissions($schoolPerms);

        $this->command->info("Role 'school_admin' created with " . count($schoolPerms) . " permissions.");
    }

    private function createOperatorRole(array $permissions): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'operator', 'guard_name' => 'web']
        );

        // Operator gets school-level permissions but limited (no delete, no finance management)
        $operatorGroups = [
            'academic_year', 'semester', 'grade_level', 'classroom', 'subject',
            'classroom_subject', 'curriculum_setting', 'student', 'ppdb_wave',
            'ppdb_registration', 'teacher', 'teaching_schedule', 'attendance_session',
            'announcement', 'book', 'book_loan', 'asset', 'facility', 'facility_booking',
            'dashboard',
        ];

        $operatorPerms = collect($permissions)
            ->only($operatorGroups)
            ->flatten()
            ->filter(fn ($perm) => ! str_starts_with($perm, 'delete_'))
            ->all();

        $role->syncPermissions($operatorPerms);

        $this->command->info("Role 'operator' created with " . count($operatorPerms) . " permissions.");
    }

    private function createTeacherRole(array $permissions): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'teacher', 'guard_name' => 'web']
        );

        // Teacher gets only teacher panel resources + dashboard
        $teacherGroups = [
            'my_schedule', 'my_attendance_session', 'student_attendance',
            'my_assessment', 'student_grade', 'dashboard',
        ];

        $teacherPerms = collect($permissions)
            ->only($teacherGroups)
            ->flatten()
            ->all();

        $role->syncPermissions($teacherPerms);

        $this->command->info("Role 'teacher' created with " . count($teacherPerms) . " permissions.");
    }

    private function assignRolesToExistingUsers(): void
    {
        $roleMap = [
            UserType::SUPER_ADMIN->value => 'super_admin',
            UserType::SCHOOL_ADMIN->value => 'school_admin',
            UserType::OPERATOR->value => 'operator',
            UserType::TEACHER->value => 'teacher',
        ];

        $assigned = 0;
        foreach ($roleMap as $userType => $roleName) {
            $users = User::where('type', $userType)->get();
            foreach ($users as $user) {
                if (! $user->hasRole($roleName)) {
                    $user->assignRole($roleName);
                    $assigned++;
                }
            }
        }

        $this->command->info("Assigned roles to {$assigned} existing users.");
    }
}
