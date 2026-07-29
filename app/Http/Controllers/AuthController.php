<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller{
    public function register(Request $request): JsonResponse{

$validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|string|min:8|confirmed',
    'phone' => 'required|string|max:20',
    'national_id' => 'required|string|unique:volunteers,national_id',
]);

$user = User::create([
    'name' => $validated['name'],
    'email' => $validated['email'],
    'password' => Hash::make($validated['password']),
    'role' => 'volunteer',
]);

$volunteer = Volunteer::create([
    'user_id' => $user->id,
    'phone' => $validated['phone'],
    'national_id' => $validated['national_id'],
]);

$token = $user->createToken('api-token')->plainTextToken;

return response()->json([
    'message' => 'Volunteer registered successfully.',
    'user' => $user,
    'token' => $token,
], 201);
}
    public function login(Request $request): JsonResponse{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($credentials)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $user = $request->user();

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
}
    public function logout(Request $request){
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logout successful'
    ]);
}

}