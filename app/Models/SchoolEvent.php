<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Agenda / academic calendar entry shown on the website and the TV display.
class SchoolEvent extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'location',
        'category',
        'starts_at',
        'ends_at',
        'is_published',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q->where('starts_at', '>=', now()->startOfDay())
            ->orWhere('ends_at', '>=', now()));
    }

    public static function categoryLabels(): array
    {
        return [
            'academic' => __('Academic'),
            'exam' => __('Exam'),
            'holiday' => __('Holiday'),
            'competition' => __('Competition'),
            'meeting' => __('Meeting'),
            'other' => __('Other'),
        ];
    }
}
