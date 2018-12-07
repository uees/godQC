<?php

namespace App\Events;

use App\ProductBatch;
use App\TestRecord;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RecordDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $batch;

    public $record;

    public function __construct(ProductBatch $batch, TestRecord $record)
    {
        $this->batch = $batch;
        $this->record = $record;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
