<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTableAddDwfw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->unsignedInteger('partner_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->string('last_device')->nullable();
            $table->unsignedInteger('profile_image_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('timezone_id')->nullable()->after('partner_id')->index()->constrained()->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('timezone_id');
            $table->dropColumn('profile_image_id');
            $table->dropColumn('partner_id');
            $table->dropColumn('last_device');
        });
    }
}
