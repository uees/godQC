<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuggestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('suggests')->insert([
            [
                'name' => '检测员',
                'data' => json_encode([
                    [
                        'name' => '周菊明',
                    ],
                    [
                        'name' => '黄桂泉',
                    ],
                    [
                        'name' => '袁秀英',
                    ],
                    [
                        'name' => '谭姣姣',
                    ],
                    [
                        'name' => '王孟娇',
                    ],
                    [
                        'name' => '欧阳泽成',
                    ],
                    [
                        'name' => '谢秀琴',
                    ],
                    [
                        'name' => '周建兰',
                    ],
                    [
                        'name' => '万重阳',
                    ],
                ])
            ],
        ]);
    }
}
