<?php

namespace XBigDaddyx\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('gatekeeper.tables.approvals', 'approvals');
    }

    protected $fillable = ['approvable_id', 'approvable_type', 'user_id', 'status', 'comment', 'action_at'];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    public function approvable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(config('gatekeeper.user_model', \App\Models\User::class));
    }

    // Accessor for model_name
    public function getModelNameAttribute(): string
    {
        return class_basename($this->approvable_type);
    }

    // Accessor for requested_by/reviewed_by (assuming user has a name attribute)
    public function getRequestedByAttribute(): string
    {
        return $this->user ? $this->user->name : 'Unknown User';
    }

    public function getReviewedByAttribute(): string
    {
        return $this->getRequestedByAttribute(); // Alias for consistency
    }
}
