<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use App\Service\ApiClient;
use Livewire\Livewire;

test('login screen can be rendered', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSeeLivewire(Login::class);
});

test('login screen contains flux components', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Log in to your account')
        ->assertSee('Email address')
        ->assertSee('Password')
        ->assertSee('Remember me');
});

test('login screen shows link to register', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Sign up');
});

test('authenticated users are redirected from login', function () {
    $user = new User(
        id: '1',
        name: 'Test User',
        email: 'test@example.com',
    );

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});

test('users can login with valid credentials', function () {
    $user = new User(
        id: '01HXTEST000000000000000001',
        name: 'Test User',
        email: 'test@example.com',
    );

    $api = $this->createMock(ApiClient::class);
    $api->expects($this->once())
        ->method('login')
        ->with('test@example.com', 'password')
        ->willReturn($user);
    $this->app->instance(ApiClient::class, $api);

    Livewire::test(Login::class)
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users cannot login with invalid credentials', function () {
    $api = $this->createMock(ApiClient::class);
    $api->expects($this->once())
        ->method('login')
        ->willThrowException(new RuntimeException('Invalid credentials.'));
    $this->app->instance(ApiClient::class, $api);

    Livewire::test(Login::class)
        ->set('email', 'test@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

test('login requires email', function () {
    Livewire::test(Login::class)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');
});

test('login requires valid email', function () {
    Livewire::test(Login::class)
        ->set('email', 'not-an-email')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');
});

test('login requires password', function () {
    Livewire::test(Login::class)
        ->set('email', 'test@example.com')
        ->call('login')
        ->assertHasErrors('password');
});

test('users can logout', function () {
    $user = new User(
        id: '1',
        name: 'Test User',
        email: 'test@example.com',
    );

    $api = $this->createMock(ApiClient::class);
    $api->expects($this->once())->method('logout');
    $this->app->instance(ApiClient::class, $api);

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});
