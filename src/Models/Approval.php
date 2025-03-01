<?php

namespace XBigDaddyx\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = config("gatekeeper.tables.approvals");
    }
    protected $fillable = ['approvable_id', 'approvable_type', 'user_id', 'status', 'comment', 'action_at'];

    public function approvable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(config('gatekeeper.user_model', \App\Models\User::class));
    }
}
