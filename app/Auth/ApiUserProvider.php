<?php

declare(strict_types=1);

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class ApiUserProvider implements UserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        return session('auth_user');
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void {}

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        return session('auth_user');
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return true;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void {}
}
