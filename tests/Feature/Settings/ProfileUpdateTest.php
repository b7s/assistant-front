<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = new User(
        id: '1',
        name: 'Test User',
        email: 'test@example.com',
    );

    $this->actingAs($user);

    $this->get(route('profile.edit'))->assertOk();
});
