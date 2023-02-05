<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Resources\BattletagResource;
use App\Models\Battletag;
use App\Models\Session;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BattletagRepository
{
    public function setFields(Battletag &$battletag, array $options): void
    {
        $battletag->battletag = $options['battletag'];
        $battletag->battletag_id = $options['battletag_id'];
        $battletag->sub = $options['sub'];
    }

    public function store(array $options): ?BattletagResource
    {
        $battletag = new Battletag();

        $this->setFields($battletag, $options);

        if ($battletag->save()) {
            return new BattletagResource($battletag);
        }

        return null;
    }

    public function getByBattletagId(string $battletag_id): ?BattletagResource
    {
        $qb = Battletag::query();

        $battletag = $qb->where('battletag_id', $battletag_id)->first();

        if ($battletag) {
            return new BattletagResource($battletag);
        }

        return null;
    }
    public function getById(string $id): ?BattletagResource
    {
        $qb = Battletag::find($id);

        $battletag = $qb->first();

        if ($battletag) {
            return new BattletagResource($battletag);
        }

        return null;
    }

    public function getBattletagSessions(string $id): ?HasMany
    {
        $qb = Battletag::query();

        $battletag = $qb->where('battletag_id', $id)->first();

        return $battletag?->sessions;

    }
}
