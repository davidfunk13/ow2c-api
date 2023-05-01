<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Controllers\NotFoundResponseTrait;
use App\Models\Battletag;
use App\Models\Session;
use Illuminate\Database\Eloquent\Collection;

class SessionRepository
{
    use NotFoundResponseTrait;

    public function setFields(Session &$session, array $options): void
    {
        $session->name = $options['name'];
    }

    public function store(string $battletag_id, array $options): ?Session
    {        
        $battletag = Battletag::find($battletag_id)->first();
        
        if (!$battletag) {
            $this->resourceNotFound("Battletag");
        }
        
        $session = new Session();
        
        $session->battletag_id = $battletag->id;

        $this->setFields($session, $options);

        if ($session->save()) {
            return $session;
        }

        return null;
    }
    public function getSessionsByBattletagId(string $battletag_id): Collection
    {
        $battletag = Battletag::find($battletag_id)->first();

        if(!$battletag){
            $this->resourceNotFound("Battletag");
        }

        return $battletag->sessions;
    }

    public function getById(string $battletag_id, string $session_id): ?Session
    {
        $session = Session::find($session_id)->where('battletag_id', $battletag_id)->first();
        
        if ($session) {
            return $session;
        }

        return null;
    }

    public function updateSession(Session $session, array $options): ?Session
    {
        $this->setFields($session, $options);

        if ($session->save()) {
            return $session;
        }

        return null;
    }

    public function destroy(Session $session): ?bool
    {
        return $session->delete();
    }
}
