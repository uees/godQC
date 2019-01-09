<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestWayId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('test_way_id')->after('id')->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('test_way_id')->after('id')->nullable();
        });

        // update test_way_id
        DB::update('UPDATE products INNER JOIN testables ON products.id = testables.testable_id SET products.test_way_id = testables.test_way_id WHERE testables.testable_type = \'App\\Product\'');
        DB::update('UPDATE categories INNER JOIN testables ON categories.id = testables.testable_id SET categories.test_way_id = testables.test_way_id WHERE testables.testable_type = \'App\\Category\'');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('test_way_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('test_way_id');
        });
    }
}
