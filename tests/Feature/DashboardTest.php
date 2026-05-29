<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = new User(
        id: '1',
        name: 'Test User',
        email: 'test@example.com',
    );

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});
