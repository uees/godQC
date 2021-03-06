<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\QCSampled' => [
            'App\Listeners\UpdateTestsNum',
        ],
        'App\Events\RecordDeleted' => [
            'App\Listeners\UpdateTestsNum',
        ],
        'App\Events\RecordArchived' => [
            'App\Listeners\ReportTask',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
