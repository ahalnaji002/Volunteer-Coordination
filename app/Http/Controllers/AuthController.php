<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = $request->user();

        return $this->success([
            'user' => (new UserResource($user))->resolve($request),
            'token' => $user->createToken('api-token')->plainTextToken,
        ], 'Login successful.');
    }

    public function user(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()),
            'Authenticated user retrieved successfully.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(message: 'Logout successful.');
    }
}
