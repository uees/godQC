<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 产品批次
        Schema::create('product_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_name', 128);
            $table->string('product_name_suffix', 64)->nullable();
            $table->string('batch_number', 32);
            $table->enum('type', ['FQC', 'IQC']);
            $table->integer('amount')->nullable();
            $table->unsignedInteger('tests_num')->default(0);
            $table->text('memo')->nullable();
            $table->timestamps();

            $table->unique(['product_name', 'product_name_suffix', 'batch_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_batches');
    }
}
