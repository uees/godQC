<?php

namespace App\Listeners;

use App\Events\QCSampled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateTestsNum implements ShouldQueue
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
     * @param  QCSampled $event
     * @return void
     */
    public function handle(QCSampled $event)
    {
        $event->batch->tests_num++;
        $event->batch->save();

        $event->record->test_times = $event->batch->testRecords()->count();
        $event->record->save();
    }
}
