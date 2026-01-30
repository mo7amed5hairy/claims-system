<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
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

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/**
 * =====================================================
 * Protected Routes (Dashboard & Claims Module)
 * =====================================================
 * 
 * All other routes are handled by ClaimsModuleServiceProvider
 * and are prefixed with 'dashboard/'
 */
