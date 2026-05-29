@props([
    'title',
    'description',
    'status' => null,
    'footerText',
    'footerLinkText',
    'footerRoute',
])

<div class="flex flex-col gap-6">
    <x-auth-header :title="$title" :description="$description" />

    <x-auth-session-status class="text-center" :status="$status" />

    {{ $slot }}

    <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
        <span>{{ $footerText }}</span>
        <flux:link :href="route($footerRoute)" wire:navigate>{{ $footerLinkText }}</flux:link>
    </div>
</div>
