<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('the login page renders for guests', function () {
    $this->get('/login')->assertOk();
});

test('a user can log in with their username', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);

    $response = $this->post('/login', [
        'identifier' => $user->username,
        'password' => 'correct-password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('a user can log in with their email', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);

    $response = $this->post('/login', [
        'identifier' => $user->email,
        'password' => 'correct-password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('login fails with the wrong password', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);

    $response = $this->post('/login', [
        'identifier' => $user->username,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('identifier');
    $this->assertGuest();
});

test('an inactive user cannot log in even with the correct password', function () {
    $user = User::factory()->inactive()->create(['password' => Hash::make('correct-password')]);

    $response = $this->post('/login', [
        'identifier' => $user->username,
        'password' => 'correct-password',
    ]);

    $response->assertSessionHasErrors('identifier');
    $this->assertGuest();
});

test('a user with no password set cannot log in via the password form', function () {
    $user = User::factory()->create(['password' => null, 'auth_provider' => 'google']);

    $response = $this->post('/login', [
        'identifier' => $user->username,
        'password' => 'anything',
    ]);

    $response->assertSessionHasErrors('identifier');
    $this->assertGuest();
});

test('a logged-in user can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/logout');

    $this->assertGuest();
});

test('an authenticated user reaches the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertOk();
});
