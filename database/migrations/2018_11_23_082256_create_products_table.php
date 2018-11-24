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
            $table->string('internal_name');
            $table->string('market_name');
            $table->string('part_a')->nullable();
            $table->string('part_b')->nullable();
            $table->string('ab_ratio')->nullable();
            $table->string('color')->nullable();
            $table->string('spec')->nullable();
            $table->string('label_viscosity')->nullable();
            $table->string('viscosity_width')->nullable();
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
