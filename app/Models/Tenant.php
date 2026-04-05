<?php

namespace App\Models;

use App\Enums\TenantStatus;
use App\Enums\SchoolType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected static ?Tenant $currentTenant = null;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'npsn',
        'school_type',
        'principal_name',
        'status',
        'trial_ends_at',
        'subscription_id',
        'settings',
    ];

    protected $casts = [
        'status' => TenantStatus::class,
        'school_type' => SchoolType::class,
        'trial_ends_at' => 'datetime',
        'settings' => 'array',
    ];

    public static function current(): ?static
    {
        return static::$currentTenant;
    }

    public static function setCurrent(?Tenant $tenant): void
    {
        static::$currentTenant = $tenant;
    }

    public static function forgetCurrent(): void
    {
        static::$currentTenant = null;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function isActive(): bool
    {
        return $this->status === TenantStatus::ACTIVE;
    }

    public function isTrial(): bool
    {
        return $this->status === TenantStatus::TRIAL;
    }

    public function isTrialExpired(): bool
    {
        return $this->isTrial() && $this->trial_ends_at?->isPast();
    }
}
