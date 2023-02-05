<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Repositories\SessionRepository;
use App\Http\Requests\Session\UpdateRequest as UpdateSessionRequest;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    protected SessionRepository $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(int $battletagId, int $sessionId, UpdateSessionRequest $request){
    
        return response()->json([
            'message' => 'Update Session',
            'battletag_id' => $battletagId,
            'session_id' => $sessionId,
        ]);
    }
}
