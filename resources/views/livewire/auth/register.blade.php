<x-auth-form
    :title="__('Create an account')"
    :description="__('Enter your details below to create your account')"
    :footerText="__('Already have an account?')"
    :footerLinkText="__('Log in')"
    footerRoute="login"
>
    <form wire:submit="register" class="flex flex-col gap-6">
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
            {{ __('Create account') }}
        </flux:button>
    </form>
</x-auth-form>
