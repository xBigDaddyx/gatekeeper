<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(config('gatekeeper.tables.approval_flows', 'approval_flows'), function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type');
            $table->foreignId('job_title_id')->nullable()->constrained(config('gatekeeper.tables.job_titles', 'job_titles'));
            $table->string('role')->nullable();
            $table->integer('step_order');
            $table->boolean('is_parallel')->default(false);
            $table->json('condition')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('gatekeeper.tables.approval_flows', 'approval_flows'));
    }
};
