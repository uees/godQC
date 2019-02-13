<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('year');
            $table->integer('month');
            $table->enum('qc_type', ['FQC', 'IQC']);
            $table->integer('category_id')->nullable();  // 分类
            $table->integer('tests_num');  // 检测数
            $table->integer('once_disqualification_num'); // 一次不合格数量
            $table->integer('disqualification_num');  // 不合格数量
            $table->integer('force_accept_num');  // 特采数量
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
        Schema::dropIfExists('test_statistics');
    }
}
