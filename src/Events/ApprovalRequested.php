<?php

namespace XBigDaddyx\Gatekeeper\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApprovalRequested
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $approvable;

    public function __construct($approvable)
    {
        $this->approvable = $approvable;
    }
}
