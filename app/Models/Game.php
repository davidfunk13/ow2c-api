<?php

namespace App\Models;

use App\Enums\DataSource;
use App\Enums\GameResult;
use App\Enums\GameStatus;
use App\Enums\QueueType;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Game extends Model
{
    use HasFactory;

    public const DETAIL_RELATIONS = [
        'map.submaps',
        'gameHeroes.hero',
        'gameRounds.roundHeroes.hero',
        'gameRounds.mapSubmap',
        'rankSnapshots',
        'heroSrSnapshots.hero',
    ];

    protected $attributes = [
        'is_placement' => false,
        'data_source' => 'manual',
        'status' => 'complete',
    ];

    protected $fillable = [
        'user_id',
        'play_session_id',
        'map_id',
        'queue_type',
        'result',
        'status',
        'role_played',
        'played_at',
        'duration_seconds',
        'is_placement',
        'data_source',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'queue_type' => QueueType::class,
            'result' => GameResult::class,
            'status' => GameStatus::class,
            'role_played' => Role::class,
            'data_source' => DataSource::class,
            'played_at' => 'datetime',
            'is_placement' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function playSession(): BelongsTo
    {
        return $this->belongsTo(PlaySession::class);
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function gameHeroes(): HasMany
    {
        return $this->hasMany(GameHero::class);
    }

    public function heroes(): BelongsToMany
    {
        return $this->belongsToMany(Hero::class, 'game_heroes');
    }

    public function gameRounds(): HasMany
    {
        return $this->hasMany(GameRound::class);
    }

    public function gamePlayers(): HasMany
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function gameCustomStats(): HasMany
    {
        return $this->hasMany(GameCustomStat::class);
    }

    public function gameGroupMembers(): HasMany
    {
        return $this->hasMany(GameGroupMember::class);
    }

    public function rankSnapshots(): HasMany
    {
        return $this->hasMany(RankSnapshot::class);
    }

    public function heroSrSnapshots(): HasMany
    {
        return $this->hasMany(HeroSrSnapshot::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function mediaAttachments(): MorphMany
    {
        return $this->morphMany(MediaAttachment::class, 'attachable');
    }
}
