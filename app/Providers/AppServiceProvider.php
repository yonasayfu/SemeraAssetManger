<?php

namespace App\Providers;

use App\Models\Staff;
use App\Policies\StaffPolicy;
use App\Models\ActivityLog;
use App\Policies\ActivityLogPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Department;
use App\Models\Asset;
use App\Models\Customer;

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
        Gate::policy(Staff::class, StaffPolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);

        RateLimiter::for('mailpit', function (Request $request) {
            return [
                Limit::perMinute(30)->by('mailpit-'.$request->ip()),
            ];
        });

        // Map morph types for polymorphic relations used across the app
        Relation::enforceMorphMap([
            'staff' => Staff::class,
            'department' => Department::class,
            'customer' => Customer::class,
            'asset' => Asset::class,
            // Add common domain models that record activity or participate in morphs
            'maintenance' => \App\Models\Maintenance::class,
            'warranty' => \App\Models\Warranty::class,
            'lease' => \App\Models\Lease::class,
            'reservation' => \App\Models\Reservation::class,
            'move' => \App\Models\Move::class,
            'checkout' => \App\Models\Checkout::class,
        ]);
    }
}
