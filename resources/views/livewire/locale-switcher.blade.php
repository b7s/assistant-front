<div class="flex items-center gap-2">
    <flux:select
        wire:model="locale"
        :placeholder="__('app.locale.language')"
        class="min-w-[140px]"
    >
        @foreach ($availableLocales as $code => $name)
            <flux:select.option value="{{ $code }}">{{ __('app.locale.' . $code) }}</flux:select.option>
        @endforeach
    </flux:select>
</div>
