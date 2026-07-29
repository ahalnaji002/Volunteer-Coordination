<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller{
    public function register(Request $request): JsonResponse{

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'national_id' => 'required|numeric|digits:9|unique:volunteers,national_id',
            'phone' => 'required|numeric|digits:10',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'volunteer',
            ]);

            Volunteer::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'],
                'national_id' => $validated['national_id'],
            ]);

        return $user;
        });

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