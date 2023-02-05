<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Models\Session;

class SessionRepository
{
    public function setFields(Session &$session, array $options): void
    {
        // $session->session_id = $options['session_id'];
        // // // $session->session = $options['session'];
        // $session->sub = $options['sub'];
    }

    public function store(int $battletag_id, array $options): ?Session
    {
        $session = new Session();

        $session->battletag_id = $battletag_id;

        $this->setFields($session, $options);

        if ($session->save()) {
            return $session;
        }

        // @codeCoverageIgnoreStart
        return null; // this actually has code coverage through mocks
        // @codeCoverageIgnoreEnd
    }

}
