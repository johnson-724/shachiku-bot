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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('remember_token');

            $table->string('account')->nullable()->after('email_verified_at');
            $table->string('bot_vendor')->after('password')->comment('bot vendor');
            $table->string('bot_vendor_id')->after('password')->comment('bot vendor id');
            $table->string('bot_chat_id')->after('password');
            $table->string('eip_vendor')->after('password');
            $table->string('company_code')->nullable()->after('password');
            $table->time('time_to_work')->after('password');
            $table->json('location')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account');
            $table->dropColumn('company_code');
            $table->dropColumn('bot_vendor');
            $table->dropColumn('bot_vendor_id');
            $table->dropColumn('bot_chat_id');
            $table->dropColumn('company_code');
            $table->dropColumn('time_to_work');
            $table->dropColumn('location');

            $table->string('email')->after('name')->unique();
            $table->timestamp('email_verified_at')->after('name')->nullable();
            $table->rememberToken()->after('name');
        });
    }
};
