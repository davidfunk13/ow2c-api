<?php

namespace App\Http\Controllers;

use App\Repositories\BattleNetRepository;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

// use BattleNetRepository;
class AuthController extends Controller
{
    protected BattleNetRepository $battleNetRepository;

    public function __construct(BattleNetRepository $battleNetRepository)
    {
        $this->$battleNetRepository = $battleNetRepository;
    }

    public function login(Request $request)
    {
        dd('wh');
        $code = $request->has("code");
        // dd($code);
        $battletag = $this->battleNetRepository->bnetAuthHelper($code);
        $issuedAt = time();

        $thirtyMinutesFromNow = (3600 / 2);

        $expirationDate = time() + $thirtyMinutesFromNow;

        $key = env('JWT_SECRET');

        $issuer = env('APP_URL') . ':' . env('APP_PORT');

        $audience = env('APP_URL') . ':' . env('APP_PORT');

        $payload = [
            'iss' => $issuer,
            'aud' => $audience,
            'iat' => $issuedAt,
            'nbf' => $issuedAt
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        // $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        return response()->json(['token' => $jwt]);
    }

    protected function test()
    {
        return response()->json("did it!");
    }
}