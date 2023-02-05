<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $battletag_id
 * @property mixed $battletag
 * @property mixed $sub
 * @method static find(string $battletag_id)
 */
class Battletag extends Model
{

    use HasFactory;
    use HasUuids;
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
