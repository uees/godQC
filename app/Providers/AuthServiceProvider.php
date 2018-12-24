<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Category' => 'App\Policies\CategoryPolicy',
        'App\Customer' => 'App\Policies\CustomerPolicy',
        'App\Product' => 'App\Policies\ProductPolicy',
        'App\ProductDispose' => 'App\Policies\ProductDisposePolicy',
        'App\Role' => 'App\Policies\RolePolicy',
        'App\User' => 'App\Policies\UserPolicy',
        'App\TestMethod' => 'App\Policies\TestMethodPolicy',
        'App\TestRecord' => 'App\Policies\TestRecordPolicy',
        'App\TestRecordItem' => 'App\Policies\TestRecordItemPolicy',
        'App\TestWay' => 'App\Policies\TestWayPolicy',
        'App\PatternTest' => 'App\Policies\PatternTestPolicy',
        'App\ProductBatch' => 'App\Policies\ProductBatchPolicy',
        'App\Suggest' => 'App\Policies\SuggestPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
