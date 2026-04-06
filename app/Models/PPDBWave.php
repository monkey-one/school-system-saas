<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Represents an enrollment wave period for the PPDB admission process
class PPDBWave extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'ppdb_waves';

    protected $fillable = [
        'tenant_id',
        'academic_year_id',
        'name',
        'quota_per_class',
        'opens_at',
        'closes_at',
        'requirements',
        'is_active',
    ];

    protected $casts = [
        'opens_at' => 'datetime',
        'closes_at' => 'datetime',
        'requirements' => 'array',
        'is_active' => 'boolean',
        'quota_per_class' => 'integer',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(PPDBRegistration::class, 'ppdb_wave_id');
    }
}
