<?php

namespace App\Enums;

enum UserType: string
{
    case SUPER_ADMIN = 'super_admin';
    case SCHOOL_ADMIN = 'school_admin';
    case OPERATOR = 'operator';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case PARENT = 'parent';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::SCHOOL_ADMIN => 'Admin Sekolah',
            self::OPERATOR => 'Operator',
            self::TEACHER => 'Guru',
            self::STUDENT => 'Siswa',
            self::PARENT => 'Orang Tua',
        };
    }
}
