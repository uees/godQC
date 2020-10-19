<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStateToTestRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_records', function (Blueprint $table) {
            $table->string('push_state')  // no_push, push_success, push_error
                ->after('is_archived')
                ->default('no_push');

            $table->boolean('is_created_doc')
                ->after('is_archived')
                ->default(false);

            $table->index(['push_state']);
            $table->index(['is_created_doc']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_records', function (Blueprint $table) {
            $table->dropIndex(['push_state']);
            $table->dropIndex(['is_created_doc']);
            $table->dropColumn(['push_state', 'is_created_doc']);
        });
    }
}
