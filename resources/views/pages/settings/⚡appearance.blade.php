<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('settings.appearance_settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('app.settings.appearance_settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('app.settings.appearance')" :subheading="__('app.settings.update_the_appearance_settings_for_your_account')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('app.appearance.light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('app.appearance.dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('app.appearance.system') }}</flux:radio>
        </flux:radio.group>
    </x-pages::settings.layout>
</section>