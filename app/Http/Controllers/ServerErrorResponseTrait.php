<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ServerErrorResponseTrait
{
    /**
     * @OA\Schema(
     *     schema="InternalServerErrorResponse",
     *     description="Internal Server Error response",
     *     type="object",
     *     @OA\Property(
     *         property="title",
     *         title="title",
     *         description="title of the response",
     *         example="Internal Server Error",
     *     ),
     *     @OA\Property(
     *         property="type",
     *         title="type",
     *         description="link to the type of response",
     *         example="https://httpstatus.es/500",
     *     ),
     *     @OA\Property(
     *         property="status",
     *         title="status",
     *         description="status of the response",
     *         example=500,
     *     ),
     *     @OA\Property(
     *         property="detail",
     *         title="detail",
     *         description="detail of the response",
     *         example="Internal server error",
     *     ),
     * )
     */
    protected function internalServerError(string $detail = 'Internal server error'): Response
    {
        return new JsonResponse([
            'title' => 'Internal Server Error',
            'type' => 'https://httpstatus.es/500',
            'status' => 500,
            'detail' => $detail,
        ], 500);
    }
}
