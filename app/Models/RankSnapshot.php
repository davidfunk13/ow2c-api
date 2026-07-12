<?php

namespace App\Models;

use App\Enums\RankTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RankSnapshot extends Model
{
    use HasFactory;

    protected $attributes = [
        'snapshot_type' => 'post_game',
    ];

    protected $fillable = [
        'user_id',
        'game_id',
        'role',
        'tier',
        'division',
        'rank_value',
        'progress_percent',
        'snapshot_type',
        'season',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'tier' => RankTier::class,
            'division' => 'integer',
            'rank_value' => 'integer',
            'progress_percent' => 'integer',
            'recorded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RankSnapshot $snapshot) {
            if ($snapshot->tier && $snapshot->rank_value === null) {
                $snapshot->rank_value = $snapshot->tier->rankValue($snapshot->division);
            }
        });

        static::updating(function (RankSnapshot $snapshot) {
            if ($snapshot->isDirty(['tier', 'division'])) {
                $snapshot->rank_value = $snapshot->tier->rankValue($snapshot->division);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
