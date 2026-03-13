<?php

use App\Models\User;

test('admin login entry redirects to login page', function () {
    $response = $this->get(route('admin.login'));

    $response->assertRedirect(route('login'));
});

test('guest is redirected when accessing admin dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('authenticated non admin is forbidden from admin dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertForbidden();
});

test('authenticated admin can access admin dashboard', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();
});
