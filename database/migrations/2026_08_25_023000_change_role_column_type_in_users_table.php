<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(100) NOT NULL DEFAULT 'fan'");
    }

    public function down()
    {
        // Keep varchar to avoid truncation
    }
};
