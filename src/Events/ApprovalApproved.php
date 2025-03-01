<?php

namespace XBigDaddyx\Gatekeeper\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApprovalApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $approvable;

    public function __construct($approvable)
    {
        $this->approvable = $approvable;
    }
}
