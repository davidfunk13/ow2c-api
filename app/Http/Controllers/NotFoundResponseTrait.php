<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

trait NotFoundResponseTrait
{
    protected function resourceNotFound(?string $resourceName = 'Resource'): JsonResponse
    {
        return new JsonResponse([
            'title' => 'Not found',
            'type' => 'https://httpstatus.es/404',
            'status' => 404,
            'detail' => "$resourceName not found",
        ], 404);
    }
}
