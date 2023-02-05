<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Repositories\SessionRepository;
use App\Http\Requests\Session\ShowRequest;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    protected SessionRepository $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(int $battletagId, int $sessionId, Request $request)
    {
        return response()->json([
            'message' => 'Show Session',
            'battletag_id' => $battletagId,
            'session_id' => $sessionId
        ]);
    }
}
