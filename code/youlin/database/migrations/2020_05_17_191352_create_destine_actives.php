<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDestineActives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_destine_actives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->default(0)->comment('类型id');
            $table->string('title')->default('')->comment('标签');
            $table->string('url')->default('')->comment('内容链接');
            $table->string('content')->default('')->comment('内容');
            $table->string('cover_url')->default('')->comment('封面链接');

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
        Schema::dropIfExists('destine_actives');
    }
}
