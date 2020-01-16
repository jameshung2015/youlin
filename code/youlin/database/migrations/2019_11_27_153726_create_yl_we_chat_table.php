<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 微信access_token保存
 *
 * Class CreateYlWeChatTable
 */
class CreateYlWeChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_we_chat', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token_type',10)->unique()->default('')->comment('微信token类型');
            $table->string('access_token')->default('')->comment('微信token');
            $table->unsignedInteger('expire_time')->default(0)->comment('微信token过期时间');
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
        Schema::dropIfExists('yl_we_chat');
    }
}
