<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait UnprocessableEntityResponseTrait
{
    public function unprocessableEntity(string $detail = 'Unable to process the contained instructions'): Response
    {
        return new JsonResponse([
            'title' => 'Unprocessable Entity',
            'type' => 'https://httpstatus.es/422',
            'status' => 422,
            'detail' => $detail,
        ], 422);
    }
}
