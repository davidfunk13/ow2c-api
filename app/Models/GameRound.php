<?php

namespace App\Models;

use App\Enums\GameResult;
use App\Enums\RoundSide;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class GameRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'round_number',
        'map_submap_id',
        'result',
        'side',
        'score_team',
        'score_enemy',
        'distance_meters',
        'checkpoints_reached',
        'is_overtime',
    ];

    protected $attributes = [
        'is_overtime' => false,
    ];

    protected function casts(): array
    {
        return [
            'result' => GameResult::class,
            'side' => RoundSide::class,
            'round_number' => 'integer',
            'score_team' => 'integer',
            'score_enemy' => 'integer',
            'checkpoints_reached' => 'integer',
            'distance_meters' => 'float',
            'is_overtime' => 'boolean',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function mapSubmap(): BelongsTo
    {
        return $this->belongsTo(MapSubmap::class);
    }

    public function roundHeroes(): HasMany
    {
        return $this->hasMany(RoundHero::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
