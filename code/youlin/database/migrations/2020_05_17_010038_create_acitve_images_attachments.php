<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcitveImagesAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_time_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('time_id')->default(0)->unsigned()->comment('活动的time_id');
            $table->string('filepath')->comment('文件路径')->default('');
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
        Schema::dropIfExists('yl_active_attachments');
    }
}
