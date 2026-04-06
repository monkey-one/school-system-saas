<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// Represents a subject entry within a student report card
class ReportCardSubject extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'report_card_id',
        'subject_id',
        'final_score',
        'letter_grade',
        'predicate',
        'description',
        'hadir',
        'sakit',
        'izin',
        'alfa',
    ];

    protected $casts = [
        'final_score' => 'decimal:2',
        'hadir' => 'integer',
        'sakit' => 'integer',
        'izin' => 'integer',
        'alfa' => 'integer',
    ];

    public function reportCard(): BelongsTo
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
