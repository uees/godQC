<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatternTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 从 2019-07-25 只存 H-8100/H-9100
        Schema::create('pattern_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_name', 64)->nullable();
            $table->string('batch_number', 64)->nullable();
            $table->string('nai_han_xing', 64)->nullable(); // 耐焊性
            $table->string('nai_rong_ji', 64)->nullable(); // 耐溶剂
            $table->string('nai_suan_jian', 64)->nullable(); // 耐酸碱
            $table->string('h12_xian_ying', 64)->nullable(); // 12h显影
            $table->string('h24_xian_ying', 64)->nullable(); // 24h显影
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
        Schema::dropIfExists('pattern_tests');
    }
}
