<?php

namespace App\Events;

use App\TestRecord;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class RecordArchived
{
    use SerializesModels, Dispatchable, InteractsWithSockets;

    public $record;

    public function __construct(TestRecord $record)
    {
        $this->record = $record;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('report-task');
    }
}