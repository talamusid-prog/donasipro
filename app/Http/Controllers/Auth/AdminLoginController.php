<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek apakah user ada dan role-nya admin
        $user = User::where('email', $credentials['email'])
                   ->where('role', 'admin')
                   ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan atau Anda bukan admin.',
            ])->withInput($request->only('email'));
        }

        // Coba login dengan password
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Password salah.',
        ])->withInput($request->only('email'));
    }
}
