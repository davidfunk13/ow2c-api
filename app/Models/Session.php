<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    use HasUuids;

    public function battletag(): BelongsTo
    {
        return $this->belongsTo(Battletag::class);
    }
}
