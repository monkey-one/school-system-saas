<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Multiple-choice question. `options` maps letters (A–E) to answer text;
// the correct letter is never sent to students.
class ExamQuestion extends Model
{
    use BelongsToTenant;

    public const LETTERS = ['A', 'B', 'C', 'D', 'E'];

    protected $fillable = [
        'tenant_id',
        'exam_id',
        'question',
        'options',
        'correct_option',
        'points',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'points' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $hidden = ['correct_option'];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    // Non-empty options in letter order.
    public function choices(): array
    {
        return collect(self::LETTERS)
            ->mapWithKeys(fn (string $letter) => [$letter => trim((string) ($this->options[$letter] ?? ''))])
            ->filter(fn (string $text) => $text !== '')
            ->all();
    }
}
