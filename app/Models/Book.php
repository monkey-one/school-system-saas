<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'isbn',
        'title',
        'author',
        'publisher',
        'year',
        'category_id',
        'stock',
        'available_stock',
        'location',
        'cover_path',
        'description',
    ];

    protected $casts = [
        'stock' => 'integer',
        'available_stock' => 'integer',
        'year' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }
}
