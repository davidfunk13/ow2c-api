<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Session;
use App\Models\Game;

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