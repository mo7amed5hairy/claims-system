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

/**
 * =====================================================
 * Drafts (Offline Autosave)
 * =====================================================
 */
Route::middleware('auth')->group(function () {
    Route::get('/drafts', [\App\Http\Controllers\DraftController::class, 'index'])->name('drafts.index');
    Route::get('/drafts/mine', [\App\Http\Controllers\DraftController::class, 'mine'])->name('drafts.mine');
    Route::post('/drafts/sync', [\App\Http\Controllers\DraftController::class, 'sync'])->name('drafts.sync');
    Route::post('/drafts/discard-by-uuid', [\App\Http\Controllers\DraftController::class, 'discardByUuid'])->name('drafts.discardByUuid');
    Route::post('/drafts/{draft}/finalize', [\App\Http\Controllers\DraftController::class, 'finalize'])->name('drafts.finalize');
    Route::post('/drafts/{draft}/discard', [\App\Http\Controllers\DraftController::class, 'discard'])->name('drafts.discard');
    Route::put('/drafts/{draft}', [\App\Http\Controllers\DraftController::class, 'update'])->name('drafts.update');
    Route::get('/drafts/{draft}/continue', [\App\Http\Controllers\DraftController::class, 'continue'])->name('drafts.continue');
});

/**
 * =====================================================
 * Project Backups (Admin only)
 * =====================================================
 */
Route::middleware('auth')->prefix('admin/backups')->name('admin.backups.')->group(function () {
    Route::get('/', [\App\Http\Controllers\BackupController::class, 'index'])->name('index');
    Route::post('/run', [\App\Http\Controllers\BackupController::class, 'run'])->name('run');
    Route::get('/download/{filename}', [\App\Http\Controllers\BackupController::class, 'download'])->name('download');
});
