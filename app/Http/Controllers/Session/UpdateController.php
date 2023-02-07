<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotFoundResponseTrait;
use App\Http\Controllers\ServerErrorResponseTrait;
use App\Http\Repositories\BattletagRepository;
use App\Http\Repositories\SessionRepository;
use App\Http\Requests\Session\UpdateRequest as UpdateSessionRequest;
use App\Http\Resources\BattletagResource;
use App\Http\Resources\SessionResource;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    protected SessionRepository $sessionRepository;
    protected BattletagRepository $battletagRepository;
    
    use NotFoundResponseTrait;
    use ServerErrorResponseTrait;

    public function __construct(SessionRepository $sessionRepository, BattletagRepository $battletagRepository)
    {
        $this->battletagRepository = $battletagRepository;
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(string $battletagId, string $sessionId, UpdateSessionRequest $request){
        $session = $this->sessionRepository->getById($battletagId, $sessionId);

        if(!$session){
            return $this->resourceNotFound('Session');
        }

        try {
            $options = $request->all();
            
            $options['battletag_id'] = $battletagId;

            $updated = $this->sessionRepository->updateSession($session, $options);
        } catch (\Throwable $exception) {
            return $this->internalServerError('Session could not be updated');
        }

        if (!$updated) {
            return $this->internalServerError('Session could not be updated');
        }

        return new SessionResource($updated);
    }
}
