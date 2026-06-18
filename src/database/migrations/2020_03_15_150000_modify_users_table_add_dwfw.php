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
            $table->unsignedInteger('partner_id')->nullable()->after('id');
            $table->foreign('partner_id')->references('id')->on('partners')->onUpdate('cascade')->onDelete('set null');
            $table->string('last_device')->nullable()->after('remember_token');
            $table->unsignedBigInteger('profile_image_id')->nullable()->after('last_device');
            $table->foreign('profile_image_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('timezone_id')->nullable()->after('partner_id')->index();
            $table->foreign('timezone_id')->references('id')->on('timezones')->onUpdate('cascade')->onDelete('set null');
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
            $table->dropColumn(['timezone_id', 'profile_image_id', 'partner_id', 'last_device']);
        });
    }
}
