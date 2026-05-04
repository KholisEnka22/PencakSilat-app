<?php

declare(strict_types=1);

namespace Src\Shared\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data    = null,
        string $message = 'Success',
        int $status    = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public static function error(
        string $message = 'Error',
        mixed $errors   = null,
        int $status     = 400,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    public static function unauthorized(
        string $message = 'Unauthorized',
    ): JsonResponse {
        return self::error($message, null, 401);
    }

    public static function forbidden(
        string $message = 'Forbidden',
    ): JsonResponse {
        return self::error($message, null, 403);
    }

    public static function notFound(
        string $message = 'Data tidak ditemukan',
    ): JsonResponse {
        return self::error($message, null, 404);
    }
}
