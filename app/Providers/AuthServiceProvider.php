<?php

namespace App\Providers;

use App\Policies\UserPolicy;
use App\User;
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
        \App\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Customer::class => \App\Policies\CustomerPolicy::class,
        \App\Product::class => \App\Policies\ProductPolicy::class,
        \App\ProductDispose::class => \App\Policies\ProductDisposePolicy::class,
        \App\Role::class => \App\Policies\RolePolicy::class,
        \App\User::class => \App\Policies\UserPolicy::class,
        \App\TestMethod::class => \App\Policies\TestMethodPolicy::class,
        \App\TestRecord::class => \App\Policies\TestRecordPolicy::class,
        \App\TestRecordItem::class => \App\Policies\TestRecordItemPolicy::class,
        \App\TestWay::class => \App\Policies\TestWayPolicy::class,
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
