<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Models\Battletag;
use CoderCat\JWKToPEM\JWKConverter;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class BattleNetRepository
{
    public function setFields(Battletag &$battletag, array $options): void
    {
        $battletag->blizz_id = $options['blizz_id'];
        $battletag->battletag = $options['battletag'];
        $battletag->sub = $options['sub'];
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
        JWT::$leeway = 6000;
        $token_uri = env('BLIZZ_TOKEN_URI');

        $identity_keys_uri = env('BLIZZ_IDENTITY_URI');

        $client = new Client();

        $blizz_response = $client->request('POST', $token_uri, $bnetParams);

        $body = $blizz_response->getBody()->getContents();

        $blizz_response = json_decode($body, true);

        $blizz_identity_response = $client->request('GET', $identity_keys_uri);

        $id_token = $blizz_response['id_token'];

        $body = $blizz_identity_response->getBody()->getContents();

        $blizz_identity_response = json_decode($body, true);

        $jwkConverter = new JWKConverter();

        $key_array = $blizz_identity_response['keys'][0];

        $PEM = $jwkConverter->toPEM($key_array);

        $decoded = JWT::decode($id_token, new Key($PEM, 'RS256'));

        return [
            'battletag' => $decoded->battle_tag,
            'blizz_id' => intval($decoded->sub),
            'sub' => $decoded->sub,
        ];
    }
}