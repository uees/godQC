<?php

namespace App\Listeners;

use App\Events\QCSampled;
use App\Events\RecordDeleted;
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
     * @param  QCSampled|RecordDeleted $event
     * @return void
     */
    public function handle($event)
    {
        $count = $event->batch->testRecords()->count();

        $event->batch->tests_num = $count;
        $event->batch->save();

        if ($event instanceof QCSampled) {
            $event->record->test_times = $count;
            $event->record->save();
        }
    }
}
