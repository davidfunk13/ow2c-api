<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Http\Repositories\GameRepository;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function __invoke(int $battletagId, int $sessionId, Request $request){
        return response()->json([
            'message' => 'all games',
            'battletag_id' => $battletagId,
            'session_id' => $sessionId         
        ]);
    }
}
