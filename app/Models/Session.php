<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Battletag;
use App\Models\Game;
/**
 * @property mixed $name
 * @property int|mixed $total_wins
 * @property int|mixed $wins
 * @property int|mixed $losses
 * @property int|mixed $draws
 * @property int|mixed $battletag_id
 * @property int|mixed $total_games
 * @method static find(string $session_id)
 */
class Session extends Model
{
    use HasFactory;

    public function battletag(): BelongsTo
    {
        return $this->belongsTo(Battletag::class);
    }
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
