<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\PPDBStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PPDBRegistration extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'ppdb_registrations';

    protected $fillable = [
        'tenant_id',
        'ppdb_wave_id',
        'registration_number',
        'full_name',
        'birth_date',
        'gender',
        'parent_name',
        'parent_phone',
        'parent_email',
        'previous_school',
        'address',
        'status',
        'notes',
        'reviewed_by',
        'reviewed_at',
        'documents',
        'student_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'gender' => Gender::class,
        'status' => PPDBStatus::class,
        'reviewed_at' => 'datetime',
        'documents' => 'array',
    ];

    public function ppdbWave(): BelongsTo
    {
        return $this->belongsTo(PPDBWave::class, 'ppdb_wave_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
