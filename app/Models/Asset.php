<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'category_id',
        'condition',
        'location',
        'quantity',
        'value',
        'acquisition_date',
        'photo',
        'description',
        'notes',
    ];

    protected $casts = [
        'condition' => AssetCondition::class,
        'quantity' => 'integer',
        'value' => 'decimal:2',
        'acquisition_date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }
}
