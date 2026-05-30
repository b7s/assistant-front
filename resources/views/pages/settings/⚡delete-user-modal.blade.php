<?php

use App\Service\ApiClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

new class extends Component {
    public string $password = '';

    public function deleteUser(ApiClient $api): void
    {
        $this->validate([
            'password' => 'required|string',
        ]);

        $api->logout();
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        $this->redirect('/', navigate: true);
    }
}; ?>

<flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
    <form method="POST" wire:submit="deleteUser" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('app.settings.delete_user_modal_heading') }}</flux:heading>

            <flux:subheading>
                {{ __('app.settings.delete_user_modal_description') }}
            </flux:subheading>
        </div>

        <flux:input wire:model="password" :label="__('app.auth_fields.password')" type="password" viewable />

        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('app.actions.cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button variant="danger" type="submit" data-test="confirm-delete-user-button">
                {{ __('app.settings.delete_account') }}
            </flux:button>
        </div>
    </form>
</flux:modal>