<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Session;
use App\Models\Game;
/**
 * @property mixed $blizz_id
 * @property mixed $battletag
 * @property mixed $sub
 * @method static find(string $battletag_id)
 */
class Battletag extends Model
{

    use HasFactory;
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}