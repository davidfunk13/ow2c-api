<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    use HasFactory;
    use HasUuids;
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
    public function battletag(): BelongsTo
    {
        return $this->belongsTo(Battletag::class);
    }
}
