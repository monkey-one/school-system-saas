<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

// A single message in a conversation thread between two users (e.g. a parent
// and a homeroom teacher). All messages of a conversation share thread_id.
// Content is plain text and must always be rendered escaped.
class Message extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'thread_id',
        'sender_id',
        'recipient_id',
        'student_id',
        'subject',
        'content',
        'attachments',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime',
    ];

    // New conversations get a thread ID and the signed-in user as sender, so
    // forms never need to expose (or trust) those fields.
    protected static function booted(): void
    {
        static::creating(function (Message $message) {
            $message->thread_id ??= (string) Str::uuid();
            $message->sender_id ??= auth()->id();
        });
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // Messages the given user sent or received.
    public function scopeInvolving(Builder $query, int $userId): Builder
    {
        return $query->where(fn (Builder $q) => $q->where('sender_id', $userId)->orWhere('recipient_id', $userId));
    }
}
