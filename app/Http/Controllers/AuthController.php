<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        User::create(array_merge(
            $request->validated(),
            ['password' => bcrypt($request->password)]
        ));

        $token = auth()->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return $this->createNewToken($token);
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!$token = auth()->attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }

    /**
     * @return UserResource
     */
    public function me(): UserResource
    {
        return UserResource::make(auth()->user());
    }

    /**
     * @return Response
     */
    public function logout(): Response
    {
        auth()->logout();

        return response()->noContent();
    }

    /**
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * @param $token
     * @return JsonResponse
     */
    private function createNewToken($token): JsonResponse
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}
