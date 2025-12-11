<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\RentalContract;
use App\Observers\RentalContractObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observer to keep property avg_rating in sync with contracts
        RentalContract::observe(RentalContractObserver::class);
    }
}
