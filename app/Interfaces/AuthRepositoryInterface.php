<?php

namespace App\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function login(array $request): array;

    public function register(array $request): array;

    public function logout(): bool;

    public function getMe(): User;
}
