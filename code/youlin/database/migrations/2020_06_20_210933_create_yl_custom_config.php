<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYlCustomConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yl_custom_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->default('')->comment('配置值');
            $table->string('name')->default('')->comment('配置名称');
            $table->timestamps();
        });

        \App\Models\CustomConfig::query()->create([
            'name' => '使用说明',
            'value' => 'http://www.baidu.com'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yl_custom_config');
    }
}
