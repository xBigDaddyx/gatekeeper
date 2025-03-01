<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table(config('gatekeeper.tables.user_table'), function (Blueprint $table) {
            $table->foreignId('job_title_id')->nullable()->constrained(config('gatekeeper.tables.job_titles'));
        });
    }

    public function down()
    {
        Schema::table(config('gatekeeper.tables.user_table'), function (Blueprint $table) {
            $table->dropColumn('job_title_id');
        });
    }
};
