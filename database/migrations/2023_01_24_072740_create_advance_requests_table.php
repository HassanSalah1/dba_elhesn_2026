<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_team_id');
            $table->integer('user_id');
            $table->integer('players_count')->default(0);
            $table->integer('escorts_count')->default(0);
            $table->decimal('cost', 16, 2);
            $table->string('location')->nullable();
            $table->string('statement')->nullable();
            $table->string('tournament')->nullable();
            $table->string('match_timing')->nullable();
            $table->string('move_date')->nullable();
            $table->string('return_date')->nullable();
            $table->string('breakfast')->nullable();
            $table->string('lunch')->nullable();
            $table->string('dinner')->nullable();
            $table->string('snacks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advance_requests');
    }
}
