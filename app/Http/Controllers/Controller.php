<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
     public static function successResponse($data = [],int $statusCode = 200): JsonResponse {
        return response()->json([
            'status' =>  'success',
            'data' => $data
        ], $statusCode);
    }

    public static function errorResponse(string $message = "Error", int $statusCode = 400, array $errors = []): JsonResponse {
        return response()->json([
            'status' => 'failed',
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
