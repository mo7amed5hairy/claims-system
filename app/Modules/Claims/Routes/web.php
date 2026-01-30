<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Claims\Http\Controllers\FlowController;
use App\Modules\Claims\Http\Controllers\ClaimController;
use App\Modules\Claims\Http\Controllers\ClaimEntityController;
use App\Modules\Claims\Http\Controllers\HospitalController;
use App\Modules\Claims\Http\Controllers\ReturnedInvoiceController;
use App\Modules\Claims\Http\Controllers\PaymentOrderController;

/**
 * =====================================================
 * Claims Module Routes
 * All routes are protected by 'auth' middleware
 * =====================================================
 */

Route::middleware('auth')->prefix('dashboard')->group(function () {
    
    // =====================================================
    // Step 1: Dashboard - Select Entity Type
    // =====================================================
    Route::get('/', [FlowController::class, 'index'])->name('dashboard');
    Route::post('flow/type', [FlowController::class, 'storeType'])->name('flow.store-type');
    
    // =====================================================
    // Step 2: Select Entity Options (Insurance/Ministry/etc)
    // =====================================================
    Route::get('flow/options', [FlowController::class, 'showOptions'])->name('flow.options');
    Route::post('flow/options', [FlowController::class, 'storeOptions'])->name('flow.store-options');
    
    // =====================================================
    // Step 3: Select Hospital & Department
    // =====================================================
    Route::get('flow/hospital', [FlowController::class, 'showHospital'])->name('flow.hospital');
    Route::post('flow/hospital', [FlowController::class, 'storeHospital'])->name('flow.store-hospital');
    
    // =====================================================
    // Step 4: Operations Panel (Claims, Returns, Payments)
    // =====================================================
    Route::get('flow/operations', [FlowController::class, 'showOperations'])->name('flow.operations');

    // =====================================================
    // Claims Management
    // =====================================================
    Route::prefix('claims')->name('claims.')->group(function () {
        Route::get('/', [ClaimController::class, 'index'])->name('index');
        Route::get('create', [ClaimController::class, 'create'])->name('create');
        Route::post('/', [ClaimController::class, 'store'])->name('store');
        Route::get('{claim}/edit', [ClaimController::class, 'edit'])->name('edit');
        Route::put('{claim}', [ClaimController::class, 'update'])->name('update');
        Route::delete('{claim}', [ClaimController::class, 'destroy'])->name('destroy');
    });

    // =====================================================
    // Returned Invoices Management
    // =====================================================
    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [ReturnedInvoiceController::class, 'index'])->name('index');
        Route::get('create', [ReturnedInvoiceController::class, 'create'])->name('create');
        Route::post('/', [ReturnedInvoiceController::class, 'store'])->name('store');
        Route::get('{return}/edit', [ReturnedInvoiceController::class, 'edit'])->name('edit');
        Route::put('{return}', [ReturnedInvoiceController::class, 'update'])->name('update');
        Route::delete('{return}', [ReturnedInvoiceController::class, 'destroy'])->name('destroy');
    });

    // =====================================================
    // Payment Orders Management
    // =====================================================
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentOrderController::class, 'index'])->name('index');
        Route::get('create', [PaymentOrderController::class, 'create'])->name('create');
        Route::post('/', [PaymentOrderController::class, 'store'])->name('store');
        Route::get('{payment}/edit', [PaymentOrderController::class, 'edit'])->name('edit');
        Route::put('{payment}', [PaymentOrderController::class, 'update'])->name('update');
        Route::delete('{payment}', [PaymentOrderController::class, 'destroy'])->name('destroy');
    });

    // =====================================================
    // Entities Management (Insurance/Ministries/etc)
    // =====================================================
    Route::prefix('entities')->name('entities.')->group(function () {
        Route::get('/', [ClaimEntityController::class, 'index'])->name('index');
        Route::get('create', [ClaimEntityController::class, 'create'])->name('create');
        Route::post('/', [ClaimEntityController::class, 'store'])->name('store');
        Route::get('{entity}/edit', [ClaimEntityController::class, 'edit'])->name('edit');
        Route::put('{entity}', [ClaimEntityController::class, 'update'])->name('update');
        Route::delete('{entity}', [ClaimEntityController::class, 'destroy'])->name('destroy');
    });

    // =====================================================
    // Hospitals & Departments Management
    // =====================================================
    Route::prefix('hospitals')->name('hospitals.')->group(function () {
        Route::get('/', [HospitalController::class, 'index'])->name('index');
        Route::post('/', [HospitalController::class, 'store'])->name('store');
        Route::post('{hospital}/departments', [HospitalController::class, 'storeDepartment'])->name('departments.store');
    });
});
