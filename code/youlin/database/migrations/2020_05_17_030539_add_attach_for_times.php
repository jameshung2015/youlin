<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttachForTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yl_user_times', function (Blueprint $table) {
            $table->integer('click')->unsigned()->default(0)->comment('总点击次数');
            $table->integer('party')->unsigned()->default(0)->comment('总参与人数');
            $table->integer('transmit')->unsigned()->default(0)->comment('总转发人数');
            $table->integer('comment')->unsigned()->default(0)->comment('总评论人数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yl_user_times', function (Blueprint $table) {
            //
        });
    }
}
