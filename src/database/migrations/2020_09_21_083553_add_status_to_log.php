<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddStatusToLog extends Migration
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function ($table) {
            $table->enum('status', ['OK', 'NOTICE', 'WARNING', 'ERROR',])->after('event')->default('OK');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function ($table) {
            $table->dropColumn('status');
        });
    }
}
