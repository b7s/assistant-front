<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use App\Service\ApiClient;
use Livewire\Livewire;

test('registration screen can be rendered', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSeeLivewire(Register::class);
});

test('registration screen contains flux components', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Create an account')
        ->assertSee('Name')
        ->assertSee('Email address')
        ->assertSee('Password')
        ->assertSee('Confirm password');
});

test('registration screen shows link to login', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Log in');
});

test('authenticated users are redirected from register', function () {
    $user = new User(
        id: '1',
        name: 'Test User',
        email: 'test@example.com',
    );

    $this->actingAs($user)
        ->get(route('register'))
        ->assertRedirect(route('dashboard'));
});

test('users can register with valid data', function () {
    $user = new User(
        id: '01HXTEST000000000000000002',
        name: 'New User',
        email: 'new@example.com',
    );

    $api = $this->createMock(ApiClient::class);
    $api->expects($this->once())
        ->method('register')
        ->with('New User', 'new@example.com', 'password123', 'password123')
        ->willReturn($user);
    $this->app->instance(ApiClient::class, $api);

    Livewire::test(Register::class)
        ->set('name', 'New User')
        ->set('email', 'new@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('registration requires name', function () {
    Livewire::test(Register::class)
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors('name');
});

test('registration requires email', function () {
    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors('email');
});

test('registration requires valid email', function () {
    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'not-an-email')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors('email');
});

test('registration requires password', function () {
    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('register')
        ->assertHasErrors('password');
});

test('registration requires password confirmation', function () {
    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'different')
        ->call('register')
        ->assertHasErrors('password');
});

test('registration requires minimum password length', function () {
    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('register')
        ->assertHasErrors('password');
});

test('registration fails when api returns error', function () {
    $api = $this->createMock(ApiClient::class);
    $api->expects($this->once())
        ->method('register')
        ->willThrowException(new RuntimeException('Email already taken.'));
    $this->app->instance(ApiClient::class, $api);

    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'taken@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors('email');

    $this->assertGuest();
});
