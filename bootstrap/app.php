<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Silence proc_open warnings commonly caused by version-detection packages in environments without Git
error_reporting(E_ALL & ~E_WARNING);

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '/login',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 419 Page Expired (CSRF Token Mismatch)
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired. Please refresh the page.'], 419);
                }

                if (auth()->check()) {
                    // Logged in user: redirect to same page with message, don't logout
                    return back()->with('error', 'انتهت الجلسة. يرجى المحاولة مرة أخرى.');
                }

                // Guest: redirect to login
                return redirect()->route('login')->with('error', 'انتهت الجلسة. يرجى تسجيل الدخول مرة أخرى.');
            }
        });
    })->create();
