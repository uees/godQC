<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('internal_name', 64)->unique();
            $table->string('market_name', 64);
            $table->string('part_a', 64)->nullable();
            $table->string('part_b', 64)->nullable();
            $table->string('ab_ratio', 16)->nullable();
            $table->string('color', 32)->nullable();
            $table->string('spec', 32)->nullable();
            $table->string('label_viscosity', 32)->nullable();
            $table->string('viscosity_width', 32)->nullable();
            $table->text('metas')->nullable();
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
        Schema::dropIfExists('products');
    }
}
