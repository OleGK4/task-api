<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Swagger documentation for task-API",
 *    description="Notes API service, main project -> https://github.com/OleGK4/task-api.git",
 *    version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function respondWithError(
        string $message,
        int $errorCode,
        array $errors = []
    ): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $errorCode);
    }

}
