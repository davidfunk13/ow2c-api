<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotFoundResponseTrait;
use App\Http\Repositories\SessionRepository;
use App\Http\Requests\Session\StoreRequest;

class StoreController extends Controller
{
    use NotFoundResponseTrait;

    protected SessionRepository $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(int $battletagId, StoreRequest $request){
        return response()->json([
            'message' => 'Store Session',
            'battletag_id' => $battletagId
        ]);
    }
}
