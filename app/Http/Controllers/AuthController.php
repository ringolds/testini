<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create([
            'name' => $validated['name'],
            'email'=> $validated['email'],
            'password' => $validated['password'],
            'google_id'=> NULL,
            'is_admin'=> FALSE,
            'is_banned'=> FALSE,
        ]);
        Auth::login($user);
        return redirect()->route('welcome');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('welcome');
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
}
