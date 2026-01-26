<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

/**
 * =====================================================
 * Authentication Routes
 * =====================================================
 */

// Login
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

// Logout
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

/**
 * =====================================================
 * Protected Routes (Dashboard & Claims Module)
 * =====================================================
 * 
 * All other routes are handled by ClaimsModuleServiceProvider
 * and are prefixed with 'dashboard/'
 */
