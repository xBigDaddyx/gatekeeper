<?php

namespace XBigDaddyx\Gatekeeper\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ProcessApproval implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    protected $approvable;

    protected $user;

    protected $action;

    protected $comment;

    public function __construct($approvable, $user, $action, $comment = null)
    {
        $this->approvable = $approvable;
        $this->user = $user;
        $this->action = $action;
        $this->comment = $comment;
    }

    public function handle()
    {
        if ($this->action === 'approve') {
            $this->approvable->approve($this->user, $this->comment, false);
        } elseif ($this->action === 'reject') {
            $this->approvable->reject($this->user, $this->comment, false);
        }
    }
}
