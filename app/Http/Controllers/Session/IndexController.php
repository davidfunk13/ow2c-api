<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Repositories\SessionRepository;
use App\Http\Controllers\ServerErrorResponseTrait;
use App\Http\Resources\SessionCollection;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    protected SessionRepository $sessionRepository;
    use ServerErrorResponseTrait;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(string $id): SessionCollection | Response
    {
        try {
            $sessions = $this->sessionRepository->getSessionsByBattletagId($id);
        } catch (\Throwable $exception) {

            return $this->internalServerError($exception->getMessage());
        }

        return new SessionCollection($sessions);
    }
}
