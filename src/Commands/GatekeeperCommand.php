<?php

namespace XBigDaddyx\Gatekeeper\Commands;

use Illuminate\Console\Command;

class GatekeeperCommand extends Command
{
    public $signature = 'gatekeeper';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
