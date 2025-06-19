<?php

namespace App\Http\Controllers\Api;

use App\Services\AuthService;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    use HttpResponse;

    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        try {
            $data = $this->authService->registerUser($request->validated());
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse(message: $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $data = $this->authService->loginUser($request->only('email', 'password'));
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                status: $e->getCode() ?: 400
            );
        }
    }
}
