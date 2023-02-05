<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Repositories\SessionRepository;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    protected SessionRepository $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(int $battletagId, Request $request){
        return response()->json([
            'message' => 'All Sessions',
            'battletag_id' => $battletagId
        ]);
    }
}
