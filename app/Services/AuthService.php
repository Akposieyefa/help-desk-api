<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

readonly class AuthService
{
    /**
     * @return JsonResponse
     */
    public function login($request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login details',
                'success' => false,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = Auth::user();

        if ($user?->email_verified_at === null) {
            return response()->json([
                'message' => 'Email must be verified',
                'success' => false,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->respondWithSanctumToken($user);
    }

    /**
     * @return UserResource
     */
    public function profile(): UserResource
    {
        return (new UserResource(Auth::user()))->additional([
            'message' => 'User profile fetched successfully',
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        if (Auth::check() && method_exists(Auth::user(), 'tokens')) {
            Auth::user()->tokens()->delete();
        }

        Auth::guard('web')->logout();

        return response()->json([
            'message' => 'Logged out successfully',
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated',
                'success' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user->tokens()->delete();

        return $this->respondWithSanctumToken($user);
    }

    protected function respondWithSanctumToken($user): JsonResponse
    {
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Authenticated successfully',
            'success' => true,
            'user' => new UserResource($user),
            'token' => $token,
        ], Response::HTTP_OK);
    }
}
