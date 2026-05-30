<?php

use App\Http\Middleware\SetLocale;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

test('set locale middleware sets app locale from session', function () {
    Session::put('locale', 'pt_BR');

    $middleware = new SetLocale;
    $request = request();

    $middleware->handle($request, fn ($request) => response()->noContent());

    expect(App::getLocale())->toBe('pt_BR');
});

test('set locale middleware falls back to config locale when session has no locale', function () {
    Session::forget('locale');

    $middleware = new SetLocale;
    $request = request();

    $middleware->handle($request, fn ($request) => response()->noContent());

    expect(App::getLocale())->toBe(config('app.locale'));
});

test('set locale middleware ignores invalid locale in session', function () {
    Session::put('locale', 'fr_FR');

    $middleware = new SetLocale;
    $request = request();

    $middleware->handle($request, fn ($request) => response()->noContent());

    expect(App::getLocale())->toBe(config('app.locale'));
});

test('locale switcher component renders with available locales', function () {
    $view = Livewire::test('locale-switcher');

    $view->assertOk();
    $view->assertSet('locale', config('app.locale'));
});

test('locale switcher component changes locale', function () {
    Livewire::test('locale-switcher')
        ->set('locale', 'pt_BR')
        ->assertSet('locale', 'pt_BR');

    expect(Session::get('locale'))->toBe('pt_BR');
});

test('locale switcher component rejects invalid locale', function () {
    $original = App::getLocale();

    Livewire::test('locale-switcher')
        ->set('locale', 'fr_FR')
        ->assertSet('locale', $original);
});

test('locale persists across requests via session', function () {
    $user = new User(
        id: '1',
        name: 'Test User',
        email: 'test@example.com',
    );

    Session::put('locale', 'pt_BR');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();

    expect(App::getLocale())->toBe('pt_BR');
});
