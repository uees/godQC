<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMixinTestRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 对应 A、B 组分混合检测记录
        Schema::create('mixin_test_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('product_id'); // part a
            $table->string('part_a_name');
            $table->string('part_a_batch')->default('');
            $table->string('part_b_name')->default('')->index();
            $table->string('part_b_batch')->default('');
            $table->enum('conclusion', ['NG', 'PASS'])->nullable();
            $table->string('testers')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('memo')->nullable();
            $table->boolean('show_reality')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->index(['is_archived']);
            $table->index(['product_id']);
            $table->index(['part_a_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mixin_test_records');
    }
}
