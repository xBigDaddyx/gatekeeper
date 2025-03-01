<?php

namespace XBigDaddyx\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalFlow extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = config('gatekeeper.tables.approval_flows');
    }

    protected $fillable = ['approvable_type', 'job_title_id', 'role', 'step_order', 'is_parallel', 'condition'];

    protected $casts = [
        'condition' => 'array',
        'is_parallel' => 'boolean',
    ];

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }
}
