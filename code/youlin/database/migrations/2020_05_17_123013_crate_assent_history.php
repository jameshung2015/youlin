<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrateAssentHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_assent_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id')->unsigned()->comment('评论id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->index(['user_id', 'comment_id']);
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
        Schema::dropIfExists('yl_assent_history');
    }
}
