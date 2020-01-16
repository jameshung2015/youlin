<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->unsignedTinyInteger('sex')->default(0)->comment('性别:值为1时是男性，值为2时是女性，值为0时是未知');
            $table->string('headimgurl')->default('')->comment('微信头像');
            $table->string('nickname')->default('')->comment('昵称');
            $table->string('mobile', 11)->default('')->comment('手机号');
            $table->string('openid')->default('')->comment('微信公众号openid');
            $table->string('wx_openid')->default('')->comment('微信小程序openid');
            $table->string('unionid', 100)->unique()->comment('微信平台唯一标识');
            $table->string('password', 32)->default('');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
