@php
$footerText = __("Don't have an account?");
@endphp

<x-auth-form
    :title="__('Log in to your account')"
    :description="__('Enter your email and password below to log in')"
    :status="session('status')"
    :footerText="$footerText"
    :footerLinkText="__('Sign up')"
    footerRoute="register"
>
    <form wire:submit="login" class="flex flex-col gap-6">
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="current-password"
            :placeholder="__('Password')"
            viewable
        />

        <flux:checkbox wire:model="remember" :label="__('Remember me')" />

        <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
            {{ __('Log in') }}
        </flux:button>
    </form>
</x-auth-form>
