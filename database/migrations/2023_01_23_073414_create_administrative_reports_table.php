<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministrativeReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrative_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_team_id');
            $table->date('date');
            $table->text('subject')->nullable();
            $table->text('events')->nullable();
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('location')->nullable();
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
        Schema::dropIfExists('administrative_reports');
    }
}
