<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDestineActiveType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_destine_active_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('标题')->default('');
            $table->string('desc')->comment('描述')->default('');
            $table->string('background_url')->comment('背景图片链接')->default('');
            $table->integer('is_close')->default(0)->comment('0 是开发， 1 是关闭');
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
        Schema::dropIfExists('destine_active_type');
    }
}
