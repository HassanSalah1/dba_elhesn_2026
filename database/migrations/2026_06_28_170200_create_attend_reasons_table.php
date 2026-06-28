<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attend_reasons', function (Blueprint $table) {
            $table->id();
            $table->integer('row_id')->unique();
            $table->text('reason')->nullable();
            $table->integer('reason_key')->nullable();
            $table->integer('the_order')->nullable();
            $table->text('global_reason')->nullable();
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
        Schema::dropIfExists('attend_reasons');
    }
}
