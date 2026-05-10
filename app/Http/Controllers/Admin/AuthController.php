<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        // Rate limiting to prevent brute force attacks
        $throttleKey = 'login_attempts:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 5, 15)) {
            Log::warning('Login rate limit exceeded', [
                'ip' => $request->ip(),
                'time' => now()
            ]);
            
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . RateLimiter::availableIn($throttleKey) . ' seconds.',
            ])->onlyInput('email');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Clear rate limiter on successful login
            RateLimiter::clear($throttleKey);
            
            $request->session()->regenerate();

            Log::info('User logged in successfully', [
                'user_id' => Auth::id(),
                'ip' => $request->ip()
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        // Increment failed attempt counter
        RateLimiter::hit($throttleKey, 60);

        Log::warning('Failed login attempt', [
            'email' => $credentials['email'],
            'ip' => $request->ip()
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Log::info('User logged out', [
            'user_id' => Auth::id(),
            'ip' => $request->ip()
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
