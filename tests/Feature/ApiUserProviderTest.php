<?php

declare(strict_types=1);

use App\Auth\ApiUserProvider;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rehydrates a JSON-serialized session user into a User instance', function (): void {
    session(['auth_user' => [
        'id' => '01a12d50-9f50-70d9-a5d7-04a98dd0277e',
        'name' => 'Bruno Test',
        'email' => 'bruno@test.local',
        'currency' => 'BRL',
        'timezone' => 'America/Sao_Paulo',
    ]]);

    $user = (new ApiUserProvider)->retrieveById('irrelevant');

    expect($user)->toBeInstanceOf(User::class)
        ->and($user)->toBeInstanceOf(Authenticatable::class)
        ->and($user->getAuthIdentifier())->toBe('01a12d50-9f50-70d9-a5d7-04a98dd0277e')
        ->and($user->email)->toBe('bruno@test.local')
        ->and($user->currency)->toBe('BRL')
        ->and($user->timezone)->toBe('America/Sao_Paulo');
});

it('returns the user untouched when the session still holds the object', function (): void {
    $stored = User::fromApi([
        'id' => '01a12d50-9f50-70d9-a5d7-04a98dd0277e',
        'name' => 'Bruno Test',
        'email' => 'bruno@test.local',
    ]);

    session(['auth_user' => $stored]);

    $user = (new ApiUserProvider)->retrieveById('irrelevant');

    expect($user)->toBe($stored);
});

it('returns null when no user is stored in the session', function (): void {
    expect((new ApiUserProvider)->retrieveById('irrelevant'))->toBeNull()
        ->and((new ApiUserProvider)->retrieveByCredentials([]))->toBeNull();
});
