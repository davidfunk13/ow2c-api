<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Controllers\NotFoundResponseTrait;
use App\Models\Battletag;
use App\Models\Session;
use Illuminate\Http\JsonResponse;

class SessionRepository
{
    use NotFoundResponseTrait;

    public function setFields(Session &$session, array $options): void
    {
        $session->name = $options['name'];
    }

    public function store(string $battletag_id, array $options): ?Session
    {
        $session = new Session();

        $battletag = new Battletag();

        $battletag = $battletag->get()->where("id", $battletag_id)->first();

        if (!$battletag) {
            $this->resourceNotFound("Battletag");
        }

        $session->battletag_id = $battletag->id;

        $this->setFields($session, $options);

        if ($session->save()) {
            return $session;
        }

        return null;
    }
    public function getSessionsByBattletagId(string $battletag_id)
    {
        $battletag = new Battletag();

        $battletag = $battletag->get()->where("id", $battletag_id)->first();

        return $battletag->sessions;
    }

    public function getById(string $battletagId, string $sessionId): ?Session
    {
        $battletagQb = Battletag::query();

        $battletag = $battletagQb->where('id', $battletagId)->first();

        if (!$battletag) {
            return null;
        }

        $sessionQb = Session::query();

        $session = $sessionQb->where('battletag_id', $battletagId)->where('id', $sessionId)->first();

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
