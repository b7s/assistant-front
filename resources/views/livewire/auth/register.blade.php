<x-auth-form
    :title="__('app.auth.create_an_account')"
    :description="__('app.auth.enter_your_details_below_to_create_your_account')"
    :footerText="__('app.auth.already_have_an_account')"
    :footerLinkText="__('app.actions.log_in')"
    footerRoute="login"
>
    <form method="POST" wire:submit="register" class="space-y-6">
        <flux:input
            wire:model="name"
            :label="__('app.auth_fields.name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('app.auth_fields.full_name')"
        />

        <flux:input
            wire:model="email"
            :label="__('app.auth_fields.email_address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <flux:input
            wire:model="password"
            :label="__('app.auth_fields.password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('app.auth_fields.password')"
            viewable
        />

        <flux:input
            wire:model="password_confirmation"
            :label="__('app.auth_fields.confirm_password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('app.auth_fields.confirm_password')"
            viewable
        />

        <flux:button type="submit" variant="primary" class="w-full" data-test="register-button">
            {{ __('app.actions.create_account') }}
        </flux:button>
    </form>
</x-auth-form>
