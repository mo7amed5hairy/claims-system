<?php

namespace App\Modules\Claims;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
