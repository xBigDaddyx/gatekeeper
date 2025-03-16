<?php

namespace XBigDaddyx\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = config('gatekeeper.tables.job_titles');
    }

    protected $fillable = ['title'];

    public function users()
    {
        return $this->hasMany(config('gatekeeper.user_model', \App\Models\User::class));
    }
}
