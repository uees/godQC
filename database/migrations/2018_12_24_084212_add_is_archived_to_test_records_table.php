<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsArchivedToTestRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_records', function (Blueprint $table) {
            $table->boolean('is_archived')
                ->after('said_package_at')
                ->default(false);

            $table->index(['is_archived', 'product_batch_id']); // testing, type
            $table->index(['product_batch_id']);
        });

        Schema::table('product_batches', function (Blueprint $table) {
            $table->index(['type']);
        });

        DB::table('test_records')
            ->whereNotNull('said_package_at')
            ->update(['is_archived' =>  true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_records', function (Blueprint $table) {
            $table->dropIndex(['is_archived', 'product_batch_id']);
            $table->dropIndex(['product_batch_id']);
            $table->dropColumn('is_archived');
        });

        Schema::table('product_batches', function (Blueprint $table) {
            $table->dropIndex(['type']);
        });
    }
}
