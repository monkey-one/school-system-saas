<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Defines a reusable notification template for automated messages
class NotificationTemplate extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'key',
        'subject',
        'body',
        'is_active',
        'channel',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
