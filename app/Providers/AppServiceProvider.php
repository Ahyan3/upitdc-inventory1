<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Staff;
use App\Models\Issuance;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


    public function boot()
    {
        // Clear cache when staff records change
        Staff::created(function () {
            Cache::forget('totalStaff');
        });
        Staff::updated(function () {
            Cache::forget('totalStaff');
        });
        Staff::deleted(function () {
            Cache::forget('totalStaff');
        });

        // Clear cache when issuances change
        $clearIssuanceCache = function () {
            Cache::forget('totalIssuedEquipment');
            Cache::forget('totalReturnedEquipment');
            Cache::forget('pendingRequests');
        };

        Issuance::created($clearIssuanceCache);
        Issuance::updated($clearIssuanceCache);
        Issuance::deleted($clearIssuanceCache);
    }
}
