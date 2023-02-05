<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Controllers\NotFoundResponseTrait;
use App\Http\Resources\Session\SessionCollection;
use App\Http\Resources\SessionResource;
use App\Models\Battletag;
use App\Models\Session;
use Illuminate\Http\JsonResponse;

class SessionRepository
{
    use NotFoundResponseTrait;

    public function setFields(Session &$session, array $options): void
    {
        $session->name = $options['name'];
        $session->total_wins = $options['total_wins'] ?? 0;
        $session->wins = $options['wins'] ?? 0;
        $session->losses = $options['losses'] ?? 0;
        $session->draws = $options['draws'] ?? 0;
        $session->battletag_id = $options['battletag_id'];
        $session->total_games = $options['total_games'] ?? 0;
    }

    public function store(string $battletag_id, array $options): ?Session
    {
        $options['battletag_id'] = $battletag_id;

        $session = new Session();

        $this->setFields($session, $options);

        if ($session->save()) {
            return $session;
        }

        return null;
    }

    public function getListByBattletagId(string $battletag_id)
    {
        $qb = Session::query();

        $sessions = $qb->where('battletag_id', $battletag_id)->get();

        return $sessions;
    }

    public function getById(string $battletagId, string $sessionId): Session|JsonResponse|null
    {
        $battletagQb = Battletag::query();

        $battletag = $battletagQb->where('id', $battletagId)->first();

        if (!$battletag) {
            return null;
        }

        $sessionQb = Session::query();

        $session = $sessionQb->where('battletag_id', $battletagId)->where('id', $sessionId)->first();

        if (!$session) {
            return null;
        }

        return $session;
    }

    public function updateSession(Session $session, array $options): ?Session
    {
        $this->setFields($session, $options);

        if ($session->save()) {
            return $session;
        }

        return null;
    }
}
