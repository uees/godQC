<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestRecordItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_record_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('test_record_id');
            $table->string('item');
            $table->string('spec')->nullable();
            $table->string('value')->nullable();
            $table->string('fake_value')->nullable();
            $table->string('conclusion')->nullable();
            $table->string('tester')->nullable();
            $table->string('memo')->nullable();
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
        Schema::dropIfExists('test_record_items');
    }
}
