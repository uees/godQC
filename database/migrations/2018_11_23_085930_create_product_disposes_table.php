<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDisposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 不合格处理记录表
        Schema::create('product_disposes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_batch_id');
            $table->unsignedBigInteger('from_record_id')->nullable();
            $table->unsignedBigInteger('to_record_id')->nullable();
            $table->text('method')->nullable();
            $table->string('author', 64)->nullable();
            $table->text('memo')->nullable();
            $table->boolean('is_done')->default(false);
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
        Schema::dropIfExists('product_disposes');
    }
}
