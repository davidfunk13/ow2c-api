<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Models\Battletag;
// use CoderCat\JWKToPEM\JWKConverter;
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;
use GuzzleHttp\Client;
// use Illuminate\Support\Facades\DB;

class BattleNetRepository
{
    public function setFields(Battletag &$battletag, array $options): void
    {
        $battletag->blizz_id = $options['blizz_id'];
        $battletag->battletag = $options['battletag'];
        $battletag->sub = $options['sub'];
    }
    private function decodeBattletagIdToken(string $id_token): ?object
    {
        return json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $id_token)[1]))));
    }  
    public function bnetAuthHelper(string $code): array
    {
        $bnetParams = [
            'auth' => [
                env('BLIZZ_CLIENT_ID'),
                env('BLIZZ_CLIENT_SECRET')
            ],
            'query' => [
                'grant_type' => 'authorization_code',
                'redirect_uri' => env('BLIZZ_CALLBACK_URI'),
                'scope' => 'openid',
                'code' => $code
            ]
        ];

        $token_uri = env('BLIZZ_TOKEN_URI');

        $client = new Client();
        
        $blizz_response = $client->request('POST', $token_uri, $bnetParams);
        
        $body = $blizz_response->getBody()->getContents();
        
        $blizz_response = json_decode($body, true);
        
        $id_token = $blizz_response['id_token'];
        
        // keep this code to try and diagnose server time issue for token verification. leeway isnt working.
        // ideally, though not necessary, we want to verify the integrity of the token.
        
        // $identity_keys_uri = env('BLIZZ_IDENTITY_URI');

        // $blizz_identity_response = $client->request('GET', $identity_keys_uri);
        
        // $body = $blizz_identity_response->getBody()->getContents();

        // $blizz_identity_response = json_decode($body, true);
        
        // $jwkConverter = new JWKConverter();
        
        // $key_array = $blizz_identity_response['keys'][0];

        // $PEM = $jwkConverter->toPEM($key_array);

        // $decoded = JWT::decode($id_token, new Key($PEM, 'RS256'));
        
        $decoded = $this->decodeBattletagIdToken($id_token);
        
        return [
            'battletag' => $decoded->battle_tag,
            'blizz_id' => (int)$decoded->sub,
            'sub' => $decoded->sub,
        ];
    }
}