<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMixinTestRecordItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mixin_test_record_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mixin_test_record_id');
            $table->string('name', 64);
            $table->text('spec')->nullable();
            $table->string('value')->nullable();
            $table->string('fake_value')->nullable();
            $table->enum('conclusion', ['NG', 'PASS'])->nullable();
            $table->string('tester', 64)->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();

            $table->unique(['mixin_test_record_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mixin_test_record_items');
    }
}
