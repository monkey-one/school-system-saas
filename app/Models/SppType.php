<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Defines a type of tuition fee with amount and frequency
class SppType extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'amount',
        'frequency',
        'applies_to',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
