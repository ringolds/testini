<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showRegister(){
        return view('auth.register');
    }

    public function showLogin(){
        return view('auth.login');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email'=> $validated['email'],
            'password' => $validated['password'],
            'google_id'=> NULL,
            'is_admin'=> FALSE,
            'is_banned'=> FALSE,
        ]);

        $bank = Bank::create([
            'name' => "Default",
            'user_id'=> $user->id,
            'description' => "Default bank created at registration",
            'public'=> FALSE,
            'collaborative'=> FALSE,
            'hidden'=> FALSE,
        ]);

        Auth::login($user);
        return redirect()->route('home');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }
        return back()-> 
            with('error', 'Invalid credentials');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->email)->first();

            if($user){
                $user->update([
                    'google_id' => $googleUser->id,
                ]);
            }
            else{
                $user = User::create([
                    'name' => $googleUser->name,
                    'password'=>NULL,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'is_admin'=> FALSE,
                    'is_banned'=> FALSE,
                ]);

                $bank = Bank::create([
                    'name' => "Default",
                    'user_id'=> $user->id,
                    'description' => "Default bank created at registration",
                    'public'=> FALSE,
                    'collaborative'=> FALSE,
                    'hidden'=> FALSE,
                ]);
            }

            Auth::login($user);
            return redirect()->route('home');

        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['login_error' => 'Google authentication failed.']);
        }
    }
}
