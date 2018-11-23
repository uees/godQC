<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestWaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 检测流程
        Schema::create('test_ways', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('way');
            $table->unsignedInteger('testable_id');
            $table->string('testable_type', '64');
            $table->timestamps();

            $table->index(['testable_type', 'testable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_ways');
    }
}
