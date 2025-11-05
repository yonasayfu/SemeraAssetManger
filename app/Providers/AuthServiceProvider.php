<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
	    public function boot(): void
	    {
	        $this->registerPolicies();

	        // Explicitly map Report policy
	        Gate::policy(\App\Models\Report::class, \App\Policies\ReportPolicy::class);
        Gate::policy(\App\Models\Vendor::class, \App\Policies\VendorPolicy::class);
        Gate::policy(\App\Models\Product::class, \App\Policies\ProductPolicy::class);
        Gate::policy(\App\Models\Contract::class, \App\Policies\ContractPolicy::class);
        Gate::policy(\App\Models\PurchaseOrder::class, \App\Policies\PurchaseOrderPolicy::class);
        Gate::policy(\App\Models\Software::class, \App\Policies\SoftwarePolicy::class);

        // Make Admin a true superuser: bypass permission checks
        Gate::before(function ($user, $ability) {
            return $user && method_exists($user, 'hasRole') && $user->hasRole('Admin') ? true : null;
        });
    }
}
