<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Service\ApiClient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(ApiClient $api): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = $api->register($this->name, $this->email, $this->password, $this->password_confirmation);
            Auth::login($user);
            $this->redirect(route('dashboard', absolute: false), navigate: true);
        } catch (\Exception $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.auth', ['title' => __('app.auth.create_an_account')]);
    }
}
