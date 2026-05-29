<?php

use App\Models\User;

test('security settings page can be rendered', function () {
    $user = new User(
        id: '1',
        name: 'Test User',
        email: 'test@example.com',
    );

    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('security.edit'));

    $response->assertOk();
});
