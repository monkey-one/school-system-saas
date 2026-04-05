<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappLog extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'to_number',
        'template',
        'message',
        'status',
        'sent_at',
        'error_message',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
