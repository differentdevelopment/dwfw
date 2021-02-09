<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdAndUpdatedAtToPasswordResets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('password_resets', function (Blueprint $table) {
            if(!Schema::hasColumn('password_resets', 'id')) {
                $table->id();
            }
            if(!Schema::hasColumn('password_resets', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
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
        Schema::table('password_resets', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('updated_at');
        });
    }
}
