<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Claims\Http\Controllers\FlowController;
use App\Modules\Claims\Http\Controllers\ClaimController;
use App\Modules\Claims\Http\Controllers\ClaimEntityController;
use App\Modules\Claims\Http\Controllers\HospitalController;
use App\Modules\Claims\Http\Controllers\ReturnedInvoiceController;
use App\Modules\Claims\Http\Controllers\PaymentOrderController;
use App\Modules\Claims\Http\Controllers\DiscountedInvoiceController;

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
    // Waiting Lists Flow Routes
    // =====================================================
    Route::get('flow/waiting-lists', [FlowController::class, 'showWaitingLists'])->name('flow.waiting-lists');
    Route::get('flow/waiting-lists/insurance', [FlowController::class, 'showWaitingListsInsurance'])->name('flow.waiting-lists-insurance');
    Route::post('flow/waiting-lists/insurance', [FlowController::class, 'storeWaitingListsInsurance'])->name('flow.store-waiting-lists-insurance');
    Route::get('flow/waiting-lists/ministry', [FlowController::class, 'showWaitingListsMinistry'])->name('flow.waiting-lists-ministry');
    Route::post('flow/waiting-lists/ministry', [FlowController::class, 'storeWaitingListsMinistry'])->name('flow.store-waiting-lists-ministry');

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
        Route::get('search-claims', [PaymentOrderController::class, 'searchClaims'])->name('search-claims');
        Route::get('search-by-electronic-invoice', [PaymentOrderController::class, 'searchByElectronicInvoice'])->name('search-by-electronic-invoice');
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

    // =====================================================
    // Discounted Invoices Management (الفواتير المخصمة) - Read Only
    // =====================================================
    Route::get('discounted-invoices', [DiscountedInvoiceController::class, 'index'])->name('discounted-invoices.index');

    // =====================================================
    // User Management (Admin Only)
    // =====================================================
    Route::middleware('can:admin-access')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
    });
});
