<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSrSnapshot extends Model
{
    use HasFactory;

    protected $attributes = [
        'snapshot_type' => 'post_game',
    ];

    protected $fillable = [
        'user_id',
        'hero_id',
        'game_id',
        'sr_value',
        'snapshot_type',
        'season',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'sr_value' => 'integer',
            'recorded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hero(): BelongsTo
    {
        return $this->belongsTo(Hero::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
