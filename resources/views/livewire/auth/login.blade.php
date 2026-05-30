<x-auth-form
    :title="__('app.auth.log_in')"
    :description="__('app.auth.enter_your_email_and_password_below_to_log_in')"
    :footerText="__('app.auth.dont_have_an_account')"
    :footerLinkText="__('app.auth.sign_up')"
    footerRoute="register"
>
    <form method="POST" wire:submit="login" class="space-y-6">
        <flux:input
            wire:model="email"
            :label="__('app.auth_fields.email_address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <flux:input
            wire:model="password"
            :label="__('app.auth_fields.password')"
            type="password"
            required
            autocomplete="current-password"
            :placeholder="__('app.auth_fields.password')"
            viewable
        />

        <flux:checkbox wire:model="remember" :label="__('app.auth.remember_me')" />

        <flux:button type="submit" variant="primary" class="w-full" data-test="login-button">
            {{ __('app.actions.log_in') }}
        </flux:button>
    </form>
</x-auth-form>
