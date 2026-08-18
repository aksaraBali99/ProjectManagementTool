<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

dataset('sqlInjectionPayloads', [
    "' OR '1'='1",
    "' OR '1'='1' -- ",
    "' OR 1=1#",
    "admin'--",
    "' UNION SELECT * FROM users -- ",
    "'; DROP TABLE users; --",
    '" OR ""="',
]);

test('SQL injection payloads in the identifier field do not bypass login', function (string $payload) {
    User::factory()->create(['password' => Hash::make('correct-password')]);

    $response = $this->post('/login', [
        'identifier' => $payload,
        'password' => 'correct-password',
    ]);

    $response->assertSessionHasErrors('identifier');
    $this->assertGuest();
})->with('sqlInjectionPayloads');

test('SQL injection payloads in the password field do not bypass login', function (string $payload) {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);

    $response = $this->post('/login', [
        'identifier' => $user->username,
        'password' => $payload,
    ]);

    $response->assertSessionHasErrors('identifier');
    $this->assertGuest();
})->with('sqlInjectionPayloads');

test('a SQL injection payload targeting the identifier field does not drop or alter the users table', function () {
    User::factory()->count(3)->create();

    $this->post('/login', [
        'identifier' => "'; DROP TABLE users; --",
        'password' => 'whatever',
    ]);

    expect(Schema::hasTable('users'))->toBeTrue()
        ->and(User::count())->toBe(3);
});

test('a tautology-based SQL injection payload does not authenticate as an arbitrary user', function () {
    User::factory()->create(['username' => 'target-user', 'password' => Hash::make('correct-password')]);

    $response = $this->post('/login', [
        'identifier' => "target-user' OR '1'='1",
        'password' => "anything' OR '1'='1",
    ]);

    $response->assertSessionHasErrors('identifier');
    $this->assertGuest();
});
