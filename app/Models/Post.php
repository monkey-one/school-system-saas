<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

// News or article shown on the public school website.
class Post extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    public const CATEGORIES = ['news', 'article', 'announcement'];

    protected $fillable = [
        'tenant_id',
        'author_id',
        'category',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'is_published',
        'is_featured',
        'published_at',
        'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer',
    ];

    // A missing slug is generated from the title and kept unique within the
    // school. Existing slugs never change, so shared links keep working.
    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if (blank($post->slug)) {
                $base = Str::slug($post->title) ?: 'post';
                $slug = $base;
                $counter = 2;

                while (static::withTrashed()->where('slug', $slug)->whereKeyNot($post->getKey())->exists()) {
                    $slug = $base . '-' . $counter++;
                }

                $post->slug = $slug;
            }

            if ($post->is_published && ! $post->published_at) {
                $post->published_at = now();
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('published_at', '<=', now());
    }

    public static function categoryLabels(): array
    {
        return [
            'news' => __('News'),
            'article' => __('Article'),
            'announcement' => __('Announcement'),
        ];
    }
}
