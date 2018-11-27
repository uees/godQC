<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => '管理员'
            ],
            [
                'name' => 'fqc',
                'display_name' => '成品检测员'
            ],
            [
                'name' => 'iqc',
                'display_name' => '来料检测员'
            ],
        ]);
    }
}
