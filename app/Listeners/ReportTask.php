<?php

namespace App\Listeners;

use Kingjian0801\LaravelCelery\Celery;
use App\Events\RecordArchived;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class ReportTask implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RecordArchived $event
     * @return void
     */
    public function handle($event)
    {
        // \Log::info($event->record->id);
        $celery = new Celery(config('celery.host'), config('celery.password'), config('celery.database'));
        $celery->PostTask('tasks.make_report', [$event->record->id], '');
    }
}