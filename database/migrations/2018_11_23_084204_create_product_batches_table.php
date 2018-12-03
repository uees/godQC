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
            $table->string('product_name');
            $table->string('batch_number');
            $table->enum('type', ['FQC', 'IQC']);
            $table->string('amount')->nullable();
            $table->unsignedInteger('tests_num')->default(0);
            $table->string('memo')->nullable();
            $table->timestamps();

            $table->unique(['product_name', 'batch_number']);
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
