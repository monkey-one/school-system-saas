<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// Student, teacher or school achievement showcased on the website.
class Achievement extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'title',
        'participant',
        'category',
        'level',
        'rank',
        'organizer',
        'achieved_at',
        'description',
        'image',
        'is_published',
    ];

    protected $casts = [
        'achieved_at' => 'date',
        'is_published' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public static function levelLabels(): array
    {
        return [
            'school' => __('School'),
            'district' => __('District'),
            'city' => __('City / Regency'),
            'province' => __('Province'),
            'national' => __('National'),
            'international' => __('International'),
        ];
    }

    public static function categoryLabels(): array
    {
        return [
            'academic' => __('Academic'),
            'sports' => __('Sports'),
            'arts' => __('Arts & Culture'),
            'religion' => __('Religion'),
            'technology' => __('Technology'),
            'other' => __('Other'),
        ];
    }
}
