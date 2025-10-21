<?php

namespace App\Http\Controllers;

use App\Enums\Agama;
use App\Enums\MaritalStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthenticateController extends Controller
{
    public function refreshCaptcha(){
        return response()->json(['captcha' => captcha_src()]);
    }
    public function index(){
        $title = 'Welcome to ' . env('APP_NAME');
        $slug = 'Please sign-in to your account and make your first submission';
        return view('pages.auth.login', compact('title', 'slug'));
    }

    public function store(Request $request) {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
            'captcha' => 'required|captcha'
        ],[
            'captcha.captcha' => 'invalid captcha',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password
        ];

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

    // register akun
     public function indexRegister(){
        $agama = Agama::cases();
        $maritalStts = MaritalStatus::cases();
        $arrJk = [
            'Pria',
            'Wanita',
        ];
        $title = 'Registrasi Akun';
        $slug = 'Lengkapi data berikut untuk membuat akun baru.';
        return view('pages.auth.register', compact('title', 'slug', 'agama', 'maritalStts', 'arrJk'));
    }

    public function storeRegister(Request $request) {
        try {
            $validators = Validator::make($request->all(), [
                'name' => 'required|string',
                'username' => 'required|unique:users,username',
                'email' => 'required|unique:users,email',
                'password' => 'required',
                'nik' => 'required|unique:users,nik',
                'no_kk' => 'required',
                'no_wa' => 'required|unique:users,no_wa',
                'name' => 'required|string',
                'gender' => 'required|in:Pria,Wanita',
                'tempat_lhr' => 'required|string',
                'tanggal_lhr' => 'required|date|before:today',
                'agama' => ['required', Rule::enum(Agama::class)],
                'status_kawin' => ['required', Rule::enum(MaritalStatus::class)],
                'pekerjaan' => 'nullable|string',
                'jabatan' => 'nullable|string',
                'alamat_ktp' => 'nullable|string',
                'alamat_dom' => 'nullable|string',
            ]);

            if($validators->fails()){
                return back()->withInput()->with('error', $validators->errors()->first());
            }

            $data = $request->all();
            $data['role'] = UserRole::PENDUDUK->value;
            $data['password'] = Hash::make($request->password);

            User::create($data);
            return redirect()->route('login')->with('success', 'Berhasil Registrasi');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    // end register akun
}
