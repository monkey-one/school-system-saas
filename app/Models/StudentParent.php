<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentParent extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'relation',
        'name',
        'nik',
        'birth_date',
        'education',
        'occupation',
        'income',
        'phone',
        'email',
        'address',
        'is_emergency_contact',
        'is_whatsapp_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_emergency_contact' => 'boolean',
        'is_whatsapp_active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
