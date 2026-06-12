<?php

use App\Models\User;
use App\Service\ApiClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    $this->apiClient = new ApiClient(
        apiUrl: 'https://api.test.com',
        clientId: 'web-app',
        clientSecret: 'secret-web-b7s-2024',
        timeout: 30,
        apiPrefixVersion: 'v1',
    );

    Cache::flush();
    Session::flush();
});

test('connect returns connection token from api', function () {
    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['token' => 'jwt-connection-token'], 200),
    ]);

    $token = $this->apiClient->connect();

    expect($token)->toBe('jwt-connection-token');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.test.com/api/v1/token/connect'
            && $request['client_id'] === 'web-app'
            && $request['client_secret'] === 'secret-web-b7s-2024';
    });
});

test('connect caches token for 23 hours', function () {
    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['token' => 'cached-token'], 200),
    ]);

    $this->apiClient->connect();
    $this->apiClient->connect();

    Http::assertSentCount(1);
});

test('connect throws exception on failure', function () {
    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    expect(fn () => $this->apiClient->connect())
        ->toThrow(RuntimeException::class, 'Failed to obtain connection token');
});

test('connect forgets cache entry when api call fails', function () {
    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    expect(fn () => $this->apiClient->connect())
        ->toThrow(RuntimeException::class, 'Failed to obtain connection token');

    expect(Cache::has('api_connection_token'))->toBeFalse();
});

test('login stores user and token in session', function () {
    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['token' => 'conn-token'], 200),
        'api.test.com/api/v1/login' => Http::response([
            'user' => ['id' => '01H', 'name' => 'Test', 'email' => 'test@test.com'],
            'token' => 'sanctum-token',
        ], 200),
    ]);

    $user = $this->apiClient->login('test@test.com', 'password');

    expect($user)->toBeInstanceOf(User::class);
    expect($user->email)->toBe('test@test.com');
    expect(Session::get('api_token'))->toBe('sanctum-token');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.test.com/api/v1/login'
            && $request->hasHeader('X-Connection-Token');
    });
});

test('login throws exception on invalid credentials', function () {
    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['token' => 'conn-token'], 200),
        'api.test.com/api/v1/login' => Http::response(['message' => 'Invalid credentials.'], 401),
    ]);

    expect(fn () => $this->apiClient->login('test@test.com', 'wrong'))
        ->toThrow(RuntimeException::class, 'Invalid credentials.');
});

test('register stores user and token in session', function () {
    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['token' => 'conn-token'], 200),
        'api.test.com/api/v1/register' => Http::response([
            'user' => ['id' => '01H', 'name' => 'New', 'email' => 'new@test.com'],
            'token' => 'sanctum-token',
        ], 200),
    ]);

    $user = $this->apiClient->register('New', 'new@test.com', 'password', 'password');

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('New');
    expect(Session::get('api_token'))->toBe('sanctum-token');
});

test('register throws exception on failure', function () {
    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['token' => 'conn-token'], 200),
        'api.test.com/api/v1/register' => Http::response(['message' => 'Email already taken.'], 422),
    ]);

    expect(fn () => $this->apiClient->register('New', 'taken@test.com', 'pw', 'pw'))
        ->toThrow(RuntimeException::class, 'Email already taken.');
});

test('logout calls api and clears session', function () {
    Session::put('auth_user', new User(id: '1', name: 'Test', email: 'test@test.com'));
    Session::put('api_token', 'sanctum-token');

    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['token' => 'conn-token'], 200),
        'api.test.com/api/v1/logout' => Http::response([], 204),
    ]);

    $this->apiClient->logout();

    expect(Session::has('auth_user'))->toBeFalse();
    expect(Session::has('api_token'))->toBeFalse();
});

test('logout clears session even when api call fails', function () {
    Session::put('auth_user', new User(id: '1', name: 'Test', email: 'test@test.com'));
    Session::put('api_token', 'sanctum-token');

    Http::fake([
        'api.test.com/api/v1/token/connect' => Http::response(['token' => 'conn-token'], 200),
        'api.test.com/api/v1/logout' => Http::response([], 500),
    ]);

    $this->apiClient->logout();

    expect(Session::has('auth_user'))->toBeFalse();
    expect(Session::has('api_token'))->toBeFalse();
});

test('user returns user from session when available', function () {
    $user = new User(id: '1', name: 'Test', email: 'test@test.com');
    Session::put('auth_user', $user);
    Session::put('api_token', 'token');

    expect($this->apiClient->user())->toBeInstanceOf(User::class);
});

test('user returns null when session is empty', function () {
    expect($this->apiClient->user())->toBeNull();
});
