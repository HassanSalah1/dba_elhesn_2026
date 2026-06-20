<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->dateTime('start_date')->change();
            $table->dateTime('end_date')->nullable()->change();
            if (!Schema::hasColumn('actions', 'location_ar')) {
                $table->string('location_ar')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('actions', 'location_en')) {
                $table->string('location_en')->nullable()->after('location_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->date('start_date')->change();
            $table->date('end_date')->nullable()->change();
            $table->dropColumn(['location_ar', 'location_en']);
        });
    }
};
