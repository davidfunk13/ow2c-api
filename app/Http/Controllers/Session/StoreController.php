<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\ServerErrorResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotFoundResponseTrait;
use App\Http\Repositories\SessionRepository;
use App\Http\Requests\Session\StoreRequest;
use App\Http\Resources\SessionResource;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{
    use NotFoundResponseTrait;
    use ServerErrorResponseTrait;

    protected SessionRepository $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(string $battletag_id, StoreRequest $request): Response | SessionResource
    {
        try {
            $session = $this->sessionRepository->store($battletag_id, $request->all());
        } catch (\Throwable $exception) {
            return $this->internalServerError('Session could not save to DB');
        }

        return new SessionResource($session);
    }
}
