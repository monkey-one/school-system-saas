<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Represents a school facility such as a room, lab, or field
class Facility extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'capacity',
        'location',
        'status',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];
}
