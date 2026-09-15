<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Catalogue of school rule violations with their discipline points.
class ViolationType extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'severity', 'points', 'description'];

    protected $casts = ['points' => 'integer'];

    public function violations(): HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }

    public static function severityLabels(): array
    {
        return ['light' => __('Light'), 'medium' => __('Medium'), 'heavy' => __('Heavy')];
    }
}
