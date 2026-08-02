<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect ke halaman login Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback setelah login Google berhasil.
     */
    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {

            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'provider' => 'google',
                'password' => Str::random(16),
            ]);
        } else {

            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'provider' => 'google',
            ]);
        }

        Auth::login($user);

        return redirect(session()->pull('url.intended', '/'));
    }

    /**
     * Logout pelanggan.
     */
    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    /**
     * Redirect Google Admin
     */
    public function adminRedirect()
    {
        return Socialite::driver('google')
            ->redirectUrl(url('/admin/auth/google/callback'))
            ->redirect();
    }

    /**
     * Callback Google Admin
     */
    public function adminCallback()
    {
        $googleUser = Socialite::driver('google')
            ->redirectUrl(url('/admin/auth/google/callback'))
            ->user();

        session([
            'admin_id' => $googleUser->getId(),
            'admin_nama' => $googleUser->getName(),
            'admin_email' => $googleUser->getEmail(),
            'admin_avatar' => $googleUser->getAvatar(),
        ]);

        return redirect('/admin/dashboard');
    }
}