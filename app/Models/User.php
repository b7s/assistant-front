<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $currency = 'USD',
        public string $timezone = 'UTC',
    ) {}

    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            currency: $data['currency'] ?? 'USD',
            timezone: $data['timezone'] ?? 'UTC',
        );
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): string
    {
        return $this->id;
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getAuthPasswordName(): string
    {
        return '';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void {}

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function initials(): string
    {
        return str($this->name)
            ->explode(' ')
            ->take(2)
            ->map(static fn ($word) => str($word)->substr(0, 1))
            ->implode('');
    }
}
