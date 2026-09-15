<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// A photo (uploaded image) or video (YouTube link) inside a gallery album.
class GalleryItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'gallery_album_id',
        'type',
        'image',
        'video_url',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id');
    }

    // YouTube video ID from any common URL form, or null for other links.
    public function youtubeId(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{11})~', $this->video_url, $matches);

        return $matches[1] ?? null;
    }
}
