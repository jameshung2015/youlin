<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYlApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_api_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('url')->nullable()->comment('请求url');
            $table->string('name',50)->default('')->comment('类型名称');
            $table->text('request')->nullable()->comment('请求');
            $table->text('response')->nullable()->comment('响应');
            $table->string('sign')->default('')->comment('标记');
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
        Schema::dropIfExists('yl_api_logs');
    }
}
