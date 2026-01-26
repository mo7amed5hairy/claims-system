<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        Log::info('=== LOGIN PAGE REQUESTED ===');
        Log::info('Is authenticated: ' . (Auth::check() ? 'YES' : 'NO'));
        if (Auth::check()) {
            Log::info('Current user: ' . Auth::user()->username);
        }
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        Log::info('=== LOGIN STORE HANDLER START ===');
        
        try {
            Log::info('Before authenticate - Is authenticated: ' . (Auth::check() ? 'YES' : 'NO'));
            
            $request->authenticate();
            
            Log::info('After authenticate - Is authenticated: ' . (Auth::check() ? 'YES' : 'NO'));
            Log::info('Current user: ' . (Auth::user() ? Auth::user()->username : 'NULL'));
        } catch (\Exception $e) {
            Log::error('❌ LOGIN EXCEPTION: ' . $e->getMessage());
            Log::error('Exception class: ' . get_class($e));
            throw $e;
        }

        Log::info('Before regenerate - Session ID: ' . session()->getId());
        $request->session()->regenerate();
        Log::info('After regenerate - Session ID: ' . session()->getId());
        
        Log::info('Redirecting to: ' . route('dashboard', absolute: false));
        Log::info('=== LOGIN STORE HANDLER END ===');

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
