<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrateUserParticipateTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_user_apply_times', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->default(0)->unsigned()->comment('用户id');
            $table->integer('time_id')->default(0)->unsigned()->comment('时光id');
            $table->string('name')->default('')->comment('姓名');
            $table->string('mobile')->default('')->comment('手机');
            $table->string('email')->default('')->comment('email');
            $table->string('we_chat')->default('')->comment('微信');
            $table->softDeletes();
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `yl_user_apply_times` comment'用户参与记录表'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yl_user_participate_times');
    }
}
