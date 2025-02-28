<?php

namespace XBigDaddyx\Gatekeeper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \XBigDaddyx\Gatekeeper\Gatekeeper
 */
class Gatekeeper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \XBigDaddyx\Gatekeeper\Gatekeeper::class;
    }
}
