<?php

namespace App\Http\Controllers;

use App\Http\Repositories\BattleNetRepository;
use App\Http\Repositories\BattletagRepository;
use App\Http\Requests\LoginRequest;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    protected BattleNetRepository $battleNetRepository;
    protected BattletagRepository $battletagRepository;

    public function __construct(BattleNetRepository $battleNetRepository, BattletagRepository $battletagRepository)
    {
        $this->battleNetRepository = $battleNetRepository;
        $this->battletagRepository = $battletagRepository;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $code = $request->get('code');

        $battletag = $this->battleNetRepository->bnetAuthHelper($code);

        $exists = $this->battletagRepository->getByBattletagId($battletag['blizz_id']);

        if (!$exists) {
            $battletag = $this->battletagRepository->store($battletag);            
        }

        if ($exists) {
            $battletag = $exists;
        }

        $issuedAt = time();

        $thirtyMinutesFromNow = (3600 / 2);

        $expirationDate = time() + $thirtyMinutesFromNow;

        $issuer = env('APP_URL') . ':' . env('APP_PORT');

        $audience = env('APP_URL') . ':' . env('APP_PORT');

        $payload = [
            'battletag' => $battletag['battletag'],
            'id' => $battletag['id'],
            'blizz_id' => $battletag['blizz_id'],
            'sub' => $battletag['sub'],
            'iss' => $issuer,
            'aud' => $audience,
            'iat' => $issuedAt,
            'nbf' => $issuedAt,
            'exp' => $expirationDate
        ];

        $key = env('JWT_SECRET');

        $jwt = JWT::encode($payload, $key, 'HS256');
        
        return response()
            ->json($battletag)
            ->withCookie(cookie('token', $jwt));
    }

    public function logout(): JsonResponse
    {
        $cookie = Cookie::forget('token');

        return response()
            ->json([
                'success' => true,
                'message' => 'Logged out successfully.
            '])
            ->withCookie($cookie);
    }
}
