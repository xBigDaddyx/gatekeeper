<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(config('teresa-gatekeeper.tables.job_titles', 'job_titles'), function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('teresa-gatekeeper.tables.job_titles', 'job_titles'));
    }
};
