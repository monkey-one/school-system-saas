<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

// Photo/video album of a school activity.
class GalleryAlbum extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'description',
        'cover_image',
        'event_date',
        'is_published',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (GalleryAlbum $album) {
            if (blank($album->slug)) {
                $base = Str::slug($album->title) ?: 'album';
                $slug = $base;
                $counter = 2;

                while (static::withTrashed()->where('slug', $slug)->whereKeyNot($album->getKey())->exists()) {
                    $slug = $base . '-' . $counter++;
                }

                $album->slug = $slug;
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class)->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
