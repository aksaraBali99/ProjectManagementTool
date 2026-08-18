<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

function mockGoogleUser(string $email, string $id = 'google-123'): void
{
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn($email);
    $socialiteUser->shouldReceive('getId')->andReturn($id);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

test('the google redirect route sends the user to Google', function () {
    $response = $this->get('/auth/google/redirect');

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('accounts.google.com');
});

test('an existing active user can log in via Google with a matching email', function () {
    $user = User::factory()->create([
        'email' => 'alex@example.com',
        'auth_provider' => 'password',
        'provider_id' => null,
    ]);
    mockGoogleUser('alex@example.com', 'google-999');

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->auth_provider)->toBe('google')
        ->and($user->fresh()->provider_id)->toBe('google-999');
});

test('google login is rejected when no account matches the email', function () {
    mockGoogleUser('nobody@example.com');

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('identifier');
    $this->assertGuest();
});

test('google login is rejected for a deactivated user', function () {
    User::factory()->inactive()->create(['email' => 'inactive@example.com']);
    mockGoogleUser('inactive@example.com');

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('identifier');
    $this->assertGuest();
});

test('google login does not overwrite an already-linked provider id', function () {
    $user = User::factory()->create([
        'email' => 'linked@example.com',
        'auth_provider' => 'google',
        'provider_id' => 'original-id',
    ]);
    mockGoogleUser('linked@example.com', 'a-different-id');

    $this->get('/auth/google/callback');

    expect($user->fresh()->provider_id)->toBe('original-id');
});
