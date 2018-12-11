<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 检测记录
        Schema::create('test_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_batch_id');
            $table->unsignedSmallInteger('test_times')->default(1);
            $table->enum('conclusion', ['NG', 'PASS'])->nullable();
            $table->string('testers')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('said_package_at')->nullable();
            $table->text('memo')->nullable();
            $table->boolean('show_reality')->default(false);
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
        Schema::dropIfExists('test_records');
    }
}
