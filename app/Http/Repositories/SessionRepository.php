<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Resources\Session\SessionCollection;
use App\Models\Session;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionRepository
{
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

//    public function getListByBattletagId(int $battletag_id, array $options): SessionCollection
//    {
//        $qb = Session::query();
//
//        $sessions = $qb->where('battletag_id', $battletag_id)->get();
//
//        return new SessionCollection($sessions);
//    }

//    public function getById(string $session_id, array $options): ?Session
//    {
//        $session = Session::find($session_id);
//
//        if ($session) {
//            return $session;
//        }
//
//        return null;
//    }
//    public function getSessionBattletag(string $session_id, array $options): ?BelongsTo
//    {
//        $session = Session::find($session_id);
//
//        if ($session) {
//            return $session->battletag;
//        }
//
//        return null;
//    }
}
