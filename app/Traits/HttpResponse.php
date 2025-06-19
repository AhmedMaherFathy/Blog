<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

trait HttpResponse
{
    public function paginatedResponse(
        LengthAwarePaginator $collection,
        string $resourceClass,
        bool $isCollection = false,
        string $message = 'fetched',
        int $code = Response::HTTP_OK
    ): JsonResponse {
        $data = [
            'data' => $isCollection ? new $resourceClass($collection->items()) : $resourceClass::collection($collection->items()),
            'links' => [
                'first' => $collection->url(1),
                'last' => $collection->url($collection->lastPage()),
                'next' => $collection->nextPageUrl(),
                'prev' => $collection->previousPageUrl(),
            ],
            'meta' => [
                'current_page' => $collection->currentPage(),
                'from' => $collection->firstItem(),
                'last_page' => $collection->lastPage(),
            ],
            'message' => $message,
            'code' => $code,
            'type' => 'success',
        ];

        return response()->json($data, $code);
    }

    public function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $code = Response::HTTP_OK,
        ?bool $showToast = null,
        array $additional = [],
    ): JsonResponse {
        $showToast = ! is_null($showToast) ? $showToast : request()->method() != 'GET';

        return response()->json(array_merge([
            'data' => $data,
            'message' => $message,
            'type' => 'success',
            'status' => $code,
            'showToast' => $showToast,
        ], $additional), $code);
    }

    public function throwValidationException(
        Validator $validator,
        ?array $error = null,
    ): void {
        $errors = $error ?: $validator->errors()->toArray();
        $errorsKeys = array_keys($errors);
        $finalErrors = [];
        $showFirstErrorOnly = false;
        for ($i = 0; $i < count($errorsKeys); $i++) {
            if ($showFirstErrorOnly && $i == 1) {
                break;
            }

            $finalErrors[$errorsKeys[$i]] = $errors[$errorsKeys[$i]][0];
        }

        throw new ValidationException(
            $validator,
            $this->validationErrorsResponse($finalErrors)
        );
    }

    /**
     * Validation Errors Response.
     */
    public function validationErrorsResponse(
        mixed $data = null,
        int $status = Response::HTTP_UNPROCESSABLE_ENTITY,
        string $message = 'validation errors',
        bool $showToast = true,
        array $additional = [],
    ): JsonResponse {
        return $this->errorResponse(
            $data,
            $status,
            $message,
            $showToast,
            $additional,
        );
    }

    public function errorResponse(
        $data = null,
        int $status = Response::HTTP_NOT_FOUND,
        string $message = 'Error Occurred',
        ?bool $showToast = null,
        array $additional = [],
    ): JsonResponse {
        $response = [
            'data' => $data,
            'message' => $message,
            'type' => 'error',
            'status' => $status,
            'showToast' => is_bool($showToast) ? $showToast : request()->method() != 'GET',
        ];

        return response()->json(
            array_merge($response, $additional),
            $response['status']
        );
    }
}
