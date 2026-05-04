<?php

declare(strict_types=1);

namespace Shared\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends Controller
{
    protected function success(
        mixed $data = null,
        string $message = 'Operation successful',
        int $statusCode = Response::HTTP_OK,
        array $meta = [],
    ): JsonResponse {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $statusCode);
    }

    protected function error(
        string $message = 'Operation failed',
        int $statusCode = Response::HTTP_BAD_REQUEST,
        array $errors = [],
        mixed $data = null,
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $statusCode);
    }

    protected function created(mixed $data, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success($data, $message, Response::HTTP_CREATED);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, Response::HTTP_NOT_FOUND);
    }

    protected function unauthorized(string $message = 'Unauthorized access'): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNAUTHORIZED);
    }

    protected function forbidden(string $message = 'Forbidden access'): JsonResponse
    {
        return $this->error($message, Response::HTTP_FORBIDDEN);
    }

    protected function validationError(string $message = 'Validation failed', array $errors = []): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    protected function paginated(
        AnonymousResourceCollection|LengthAwarePaginator $resource,
        string $message = 'Data retrieved successfully',
    ): JsonResponse {
        if ($resource instanceof AnonymousResourceCollection) {
            $response = $resource->response()->getData(true);

            return $this->success(
                data: $response['data'] ?? [],
                message: $message,
                meta: [
                    'links' => $response['links'] ?? [],
                    'meta' => $response['meta'] ?? [],
                ],
            );
        }

        return $this->success(
            data: $resource->items(),
            message: $message,
            meta: [
                'current_page' => $resource->currentPage(),
                'last_page' => $resource->lastPage(),
                'per_page' => $resource->perPage(),
                'total' => $resource->total(),
            ],
        );
    }
}
