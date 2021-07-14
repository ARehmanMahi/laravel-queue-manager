<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('job_id')->unique();
            $table->string('uuid', '36')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->datetime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs_log');
    }
}
