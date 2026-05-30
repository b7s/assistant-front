<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Service\ApiClient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(ApiClient $api): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = $api->login($this->email, $this->password);
            Auth::login($user, $this->remember);
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        } catch (\Exception $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.auth', ['title' => __('app.auth.log_in')]);
    }
}
