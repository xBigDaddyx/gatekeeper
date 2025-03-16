<?php

namespace XBigDaddyx\Gatekeeper\Traits;

use XBigDaddyx\Gatekeeper\Models\JobTitle;

trait HasJobTitle
{
    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }
}
