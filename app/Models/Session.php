<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Battletag;
use App\Models\Game;

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
