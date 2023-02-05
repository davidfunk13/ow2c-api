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
}
