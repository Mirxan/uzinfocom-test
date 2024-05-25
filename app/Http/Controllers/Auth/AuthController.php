<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use App\Http\Requests\AuthRequest;
use App\Interfaces\AuthRepositoryInterface;

class AuthController extends Controller
{
    public function __construct(private AuthRepositoryInterface $authInterface)
    {
        $this->authInterface = $authInterface;
    }

    public function register(AuthRequest $request): array
    {
        return response()->successResponse($this->authInterface->register($request->validated()));
    }

    public function login(AuthRequest $request): array
    {
        return response()->successResponse($this->authInterface->login($request->validated()));
    }

    public function logout(): array
    {
        return response()->successResponse($this->authInterface->logout());
    }

    public function getMe(): array
    {
        return response()->successResponse($this->authInterface->getMe());
    }
}
