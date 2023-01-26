<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $issuedAt = time();
            $expirationDate = time() * 3600; // 1hr

            //code...
        } catch (\Throwable $e) {
            //throw $th;
        }
        $key = 'example_key';
        $payload = array(
            'iss' => 'http://localhost:3001',
            'aud' => 'http://localhost:3001',
            'iat' => 1356999524,
            'nbf' => 1357000000
        );



        return response()->json("done");
    }
}
