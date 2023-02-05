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

    public function __invoke(Request $request){
        return response()->json(['message' => 'all']);
    }
}
