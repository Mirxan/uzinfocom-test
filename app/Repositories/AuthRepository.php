<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{

    public function __construct(private User $user)
    {
        $this->user = $user;
    }

    public function login(array $request = []): array
    {
        if (!$token = auth()->attempt($request)) {
            abort(response()->errorResponse("Unauthorized!", 401));
        }

        return $this->respondWithToken($token);
    }

    public function register(array $request): array
    {
        $user = $this->user->create($request);

        return [
            "user" => $user,
            "token" => $this->respondWithToken(auth()->login($user)),
        ];
    }

    public function logout(): bool
    {
        auth()->logout();
        return true;
    }

    public function getMe(): User
    {
        return auth()->user();
    }


    private function respondWithToken($token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
