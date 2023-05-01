<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Resources\BattletagResource;
use App\Models\Battletag;

class BattletagRepository
{
    public function setFields(Battletag &$battletag, array $options): void
    {
        $battletag->battletag = $options['battletag'];
        $battletag->blizz_id = $options['blizz_id'];
        $battletag->sub = $options['sub'];
    }

    public function store(array $options): ?Battletag
    {
        $battletag = new Battletag();

        $this->setFields($battletag, $options);

        if ($battletag->save()) {
            return $battletag;
        }

        return null;
    }

    public function getByBattletagId(string $blizz_id): ?Battletag
    {
        $qb = Battletag::query();
        
        // this is not your uuid. this is blizzard's.
        $battletag = $qb->where('blizz_id', $blizz_id)->first();

        if ($battletag) {
            return $battletag;
        }

        return null;
    }  

    public function getById(string $battletag_id): ?Battletag
    {
        $qb = Battletag::query();
        
        // this is not your uuid. this is blizzard's.
        $battletag = $qb->where('id', $battletag_id)->first();

        if ($battletag) {
            return $battletag;
        }

        return null;
    }  
}
