<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(config('gatekeeper.tables.approvals', 'approvals'), function (Blueprint $table) {
            $table->id();
            $table->uuidMorphs('approvable');
            $table->foreignId('user_id')->constrained();
            $table->string('status');
            $table->text('comment')->nullable();
            $table->timestamp('action_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('gatekeeper.tables.approvals', 'approvals'));
    }
};
