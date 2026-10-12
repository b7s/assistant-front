<?php

declare(strict_types=1);

namespace App\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class ApiUserProvider implements UserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        return $this->hydrate(session('auth_user'));
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void {}

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        return $this->hydrate(session('auth_user'));
    }

    /**
     * Rehydrate the session user, which the default JSON session
     * serialization stores as a plain array instead of the User object.
     */
    private function hydrate(mixed $data): ?Authenticatable
    {
        if ($data === null) {
            return null;
        }

        if ($data instanceof Authenticatable) {
            return $data;
        }

        return User::fromApi($data);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return true;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void {}
}
