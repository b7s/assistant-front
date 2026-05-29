<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use RuntimeException;

readonly class ApiClient
{
    public function __construct(
        private string $apiUrl,
        private string $clientId,
        private string $clientSecret,
        private int $timeout,
    ) {}

    public function connect(): string
    {
        return Cache::remember('api_connection_token', now()->addHours(23), function (): string {
            $response = $this->baseRequest()
                ->post('/api/token/connect', [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

            if ($response->failed()) {
                Cache::forget('api_connection_token');

                throw new RuntimeException('Failed to obtain connection token: '.$response->body());
            }

            return $response->json('token');
        });
    }

    /**
     * @throws ConnectionException
     */
    public function login(string $email, string $password): User
    {
        $response = $this->connectedRequest()
            ->post('/api/login', compact('email', 'password'));

        if ($response->failed()) {
            throw new RuntimeException($response->json('message', 'Invalid credentials.'));
        }

        $user = User::fromApi($response->json('user'));
        $this->storeSession($user, $response->json('token'));

        return $user;
    }

    /**
     * @throws ConnectionException
     */
    public function register(string $name, string $email, string $password, string $passwordConfirmation): User
    {
        $response = $this->connectedRequest()
            ->post('/api/register', [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('message', 'Registration failed.'));
        }

        $user = User::fromApi($response->json('user'));
        $this->storeSession($user, $response->json('token'));

        return $user;
    }

    public function logout(): void
    {
        try {
            $this->authenticatedRequest()->post('/api/logout');
        } finally {
            Session::forget(['auth_user', 'api_token']);
        }
    }

    public function user(): ?User
    {
        if (Session::has('auth_user') && Session::has('api_token')) {
            return Session::get('auth_user');
        }

        return null;
    }

    public function get(string $path): array
    {
        return $this->authenticatedRequest()->get($path)->json();
    }

    public function post(string $path, array $data = []): array
    {
        return $this->authenticatedRequest()->post($path, $data)->json();
    }

    private function baseRequest(): PendingRequest
    {
        return Http::baseUrl($this->apiUrl)
            ->timeout($this->timeout)
            ->acceptJson();
    }

    private function connectedRequest(): PendingRequest
    {
        return $this->baseRequest()
            ->withHeader('X-Connection-Token', $this->connect());
    }

    private function authenticatedRequest(): PendingRequest
    {
        return $this->connectedRequest()
            ->withToken(Session::get('api_token'));
    }

    private function storeSession(User $user, string $token): void
    {
        Session::put('auth_user', $user);
        Session::put('api_token', $token);
    }
}
