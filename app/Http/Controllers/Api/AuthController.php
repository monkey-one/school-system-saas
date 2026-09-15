<?php

namespace App\Http\Controllers\Api;

use App\Enums\TenantStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// Handles API authentication including login, logout, and user profile retrieval.
// The login route is rate limited (see the "api-login" limiter).
class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = User::withoutGlobalScopes()->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('The provided credentials are incorrect.')],
            ]);
        }

        if (! $user->is_active || $user->tenant?->status === TenantStatus::SUSPENDED) {
            return response()->json(['message' => __('This account is not active.')], 403);
        }

        $token = $user->createToken($request->input('device_name', 'api-token'))->plainTextToken;

        return response()->json([
            'message' => __('Login successful.'),
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('student', 'teacher');

        return response()->json([
            'data' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => __('Logged out.'),
        ]);
    }
}
