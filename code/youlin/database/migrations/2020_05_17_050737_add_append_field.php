<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAppendField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yl_comments', function (Blueprint $table) {
            $table->string('nickname')->after('user_id')->comment('微信昵称');
            $table->string('header')->after('nickname')->comment('微信头像');
        });

        Schema::table('yl_user_apply_times', function (Blueprint $table) {
            $table->string('nickname')->after('user_id')->comment('微信昵称');
            $table->string('header')->after('nickname')->comment('微信头像');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
