<?php

namespace App\Modules\Claims;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

// Models & Policies
use App\Modules\Claims\Models\Claim;
use App\Modules\Claims\Policies\ClaimPolicy;
use App\Modules\Claims\Models\ReturnedInvoice;
use App\Modules\Claims\Policies\ReturnedInvoicePolicy;
use App\Modules\Claims\Models\PaymentOrder;
use App\Modules\Claims\Policies\PaymentOrderPolicy;
use App\Modules\Claims\Models\Hospital;
use App\Modules\Claims\Policies\HospitalPolicy;
use App\Modules\Claims\Models\ClaimEntity;
use App\Modules\Claims\Policies\EntityPolicy;

// Repositories
use App\Modules\Claims\Repositories\HospitalRepository;
use App\Modules\Claims\Repositories\ClaimRepository;
use App\Modules\Claims\Repositories\EntityRepository;
use App\Modules\Claims\Repositories\ReturnedInvoiceRepository;
use App\Modules\Claims\Repositories\PaymentOrderRepository;

// Interfaces
use App\Modules\Claims\Interfaces\HospitalRepositoryInterface;
use App\Modules\Claims\Interfaces\ClaimRepositoryInterface;
use App\Modules\Claims\Interfaces\ClaimEntityRepositoryInterface;

/**
 * Claims Module Service Provider
 * 
 * Registers and bootstraps the Claims module
 */
class ClaimsModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Repository Bindings
        $this->app->bind(HospitalRepositoryInterface::class, HospitalRepository::class);
        $this->app->bind(ClaimRepositoryInterface::class, ClaimRepository::class);
        $this->app->bind(ClaimEntityRepositoryInterface::class, EntityRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $moduleName = 'claims';

        // Load Views
        $this->loadViewsFrom(__DIR__ . '/Resources/Views', $moduleName);

        // Load Migrations (optional if they stay in database/migrations)
        // $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Load Routes
        $this->registerRoutes();

        // Register Policies
        $this->registerPolicies();
    }

    /**
     * Register module policies
     */
    protected function registerPolicies()
    {
        Gate::policy(Claim::class, ClaimPolicy::class);
        Gate::policy(ReturnedInvoice::class, ReturnedInvoicePolicy::class);
        Gate::policy(PaymentOrder::class, PaymentOrderPolicy::class);
        Gate::policy(Hospital::class, HospitalPolicy::class);
        Gate::policy(ClaimEntity::class, EntityPolicy::class);

        // Define gate for admin-only access
        Gate::define('admin-access', function ($user) {
            return $user->role === 'admin';
        });
    }

    /**
     * Register module routes
     */
    protected function registerRoutes()
    {
        Route::middleware(['web', 'auth'])
            ->group(__DIR__ . '/Routes/web.php');
    }
}
