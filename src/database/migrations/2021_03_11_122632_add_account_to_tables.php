<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Alitaknak volt egy ilyen terve, hogy mindenhez kotunk/kothetunk accountot, de végül nem lett belőle semmi
        // ezt a fájlt itthagyom, hogy erre emlékeztessen - Hoagie 2021-09-08

//        Schema::table('partners', function (Blueprint $table) {
//            $table->foreignId('account_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
//        });
//        Schema::table('files', function (Blueprint $table) {
//            $table->foreignId('account_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
//        });
//        Schema::table('settings', function (Blueprint $table) {
//            $table->foreignId('account_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
//        });
//        Schema::table('logs', function (Blueprint $table) {
//            $table->foreignId('account_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('partners', function (Blueprint $table) {
//            $table->dropForeign('account_id');
//        });
//        Schema::table('files', function (Blueprint $table) {
//            $table->dropForeign('account_id');
//        });
//        Schema::table('settings', function (Blueprint $table) {
//            $table->dropForeign('account_id');
//        });
//        Schema::table('logs', function (Blueprint $table) {
//            $table->dropForeign('account_id');
//        });
    }
}
