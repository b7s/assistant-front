<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    public string $locale = '';

    public function mount(): void
    {
        $this->locale = App::getLocale();
    }

    public function updatedLocale(string $value): void
    {
        $availableLocales = array_keys(config('app.available_locales', []));

        if (! in_array($value, $availableLocales, true)) {
            $this->locale = App::getLocale();

            return;
        }

        Session::put('locale', $value);
        App::setLocale($value);

        $this->js('window.location.reload()');
    }

    public function render()
    {
        return view('livewire.locale-switcher', [
            'availableLocales' => config('app.available_locales'),
        ]);
    }
}
