<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use HttpResponse;

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->successResponse([
            'user' => $user,
            'authorization' =>
                [
                    'token' => $token,
                    'type' => 'bearer',
                ]
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse(message : 'Invalid credentials');
            }
        }
        catch (JWTException) {
            return $this->errorResponse(message : 'Could not create token' , status: 500);
        }

        return $this->successResponse([
            'authorization' =>
                [
                    'token' => $token,
                    'type' => 'bearer',
                ]
        ]);
    }
}
