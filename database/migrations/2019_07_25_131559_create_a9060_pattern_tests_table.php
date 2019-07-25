<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateA9060PatternTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 湿膜的刑式检验
        Schema::create('a9060_pattern_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_name', 64)->nullable();
            $table->string('batch_number', 64)->nullable();
            $table->string('ge_ye_xian_ying', 64)->nullable(); // 隔夜显影
            $table->string('ge_ye_bao_guang', 64)->nullable(); // 隔夜曝光
            $table->string('die_ban', 64)->nullable(); // 叠板
            $table->string('lao_hua', 64)->nullable(); // 老化
            $table->string('tester', 64)->nullable();
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
        Schema::dropIfExists('a9060_pattern_tests');
    }
}
