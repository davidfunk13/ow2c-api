<?php

namespace App\Http\Controllers;

use App\Http\Repositories\BattleNetRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

// use BattleNetRepository;
class AuthController extends Controller
{
    protected BattleNetRepository $battleNetRepository;

    public function __construct(BattleNetRepository $battleNetRepository)
    {
        $this->battleNetRepository = $battleNetRepository;
    }

    public function login(Request $request)
    {
        if($request->has("code")){
            $code = $request->get('code');
        }
        
        $battletag = $this->battleNetRepository->bnetAuthHelper($code);
        
        $issuedAt = time();
        
        $thirtyMinutesFromNow = (3600 / 2);
        
        $expirationDate = time() + $thirtyMinutesFromNow;
        
        $issuer = env('APP_URL') . ':' . env('APP_PORT');
        
        $audience = env('APP_URL') . ':' . env('APP_PORT');
        
        $payload = [
            'battletag'=> $battletag['battletag'],
            'battletag_id'=> $battletag['battletag_id'],
            'sub'=> $battletag['sub'],
            'iss' => $issuer,
            'aud' => $audience,
            'iat' => $issuedAt,
            'nbf' => $issuedAt,
            'exp' => $expirationDate
        ];
        
        $key = env('JWT_SECRET');

        $jwt = JWT::encode($payload, $key, 'HS256');

        return response()->json([
            'token' => $jwt,
            'battletag' => $battletag
        ]);
    }

    public function test()
    {
        return response()->json("did it!");
    }
}
