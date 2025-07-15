<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateController extends Controller
{
    public function index(){
        $title = 'Welcome to ' . env('APP_NAME');
        $slug = 'Please sign-in to your account and make your first submission';
        return view('pages.auth.login', compact('title', 'slug'));
    }

    public function store(Request $request) {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'username' => 'Username atau password salah',
            ])->onlyInput('username');
        }

        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    public function destroy(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('dashboard');
    }
}
