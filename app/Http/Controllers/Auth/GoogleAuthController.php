<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return redirect()->route('login')->withErrors([
                'identifier' => 'Google sign-in failed. Please try again.',
            ]);
        }

        $user = User::whereHas('emails', fn ($query) => $query->where('email', $googleUser->getEmail()))->first();

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'identifier' => 'No account found for this email — contact your admin.',
            ]);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors([
                'identifier' => 'Your account has been deactivated. Contact your administrator.',
            ]);
        }

        if (! $user->provider_id) {
            $user->update([
                'auth_provider' => 'google',
                'provider_id' => $googleUser->getId(),
            ]);
        }

        Auth::login($user);

        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
