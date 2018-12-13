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
        Schema::create('pattern_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_name', 64);
            $table->string('batch_number', 64);
            $table->string('nai_han_xing', 64);
            $table->string('h12_xian_ying', 64);
            $table->string('h24_xian_ying', 64);
            $table->string('ge_ye_xian_ying', 64);
            $table->string('ge_ye_bao_guang', 64);
            $table->string('die_ban', 64);
            $table->string('lao_hua', 64);
            $table->string('tester', 64);
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
