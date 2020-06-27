<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('time_id')->unsigned()->default(0)->comment('时光id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->string('content')->default('')->comment('评论');
            $table->integer('revert_id')->default(0)->comment('回复id')->unsigned();
            $table->integer('assent')->default(0)->comment('总点赞数量')->unsigned();

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
        Schema::dropIfExists('yl_time_comments');
    }
}
