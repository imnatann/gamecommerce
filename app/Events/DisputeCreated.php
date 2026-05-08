<?php

namespace App\Events;

use App\Models\Dispute;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DisputeCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Dispute $dispute;

    public function __construct(Dispute $dispute)
    {
        $this->dispute = $dispute;
    }
}