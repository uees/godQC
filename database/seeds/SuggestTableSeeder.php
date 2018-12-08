<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Suggest;

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

        $qcItem = new Suggest;
        $qcItem->fill(['name' => '检测项目']);
        $qcItem->save();

        $qcItem->children()->saveMany([
            new Suggest([
                'name' => '细度',
                'data' => range(5, 50, 2.5),
            ]),
            new Suggest([
                'name' => '颜色',
                'data' => [
                    'PASS',
                    '稍浅',
                    '稍深',
                    '浅了',
                    '深了',
                    '偏黄',
                    '偏绿',
                    '偏红',
                    '偏蓝',
                    '偏紫',
                    '偏黑',
                    '发白',
                    '色相不一样',
                ],
            ]),
            new Suggest([
                'name' => '6转粘度',
                'data' => null,
            ]),
            new Suggest([
                'name' => '60转粘度',
                'data' => null,
            ]),
            new Suggest([
                'name' => '红外匹配度',
                'data' => null,
            ]),
            new Suggest([
                'name' => '板面状态',
                'data' => [
                    'PASS',
                    '有气泡',
                    '气泡破裂烤后成黑点',
                    '线路间的气泡破后呈现针孔',
                    '有白点',
                    '气泡白点都有',
                    '有明显垃圾',
                    '哑光油是亮光',
                    '光泽偏亮',
                    '光泽差',
                    '流平差'
                ],
            ]),
            new Suggest([
                'name' => '固化性',
                'data' => [
                    'PASS',
                    '烤不干',
                    '粘菲林',
                    '菲林痕迹明显',
                ],
            ]),
            new Suggest([
                'name' => '常温五分钟流油',
                'data' => null,
            ]),
            new Suggest([
                'name' => '75℃烤十分钟流油',
                'data' => null,
            ]),
            new Suggest([
                'name' => '感光性',
                'data' => range(0,24, 0.5),
            ]),
            new Suggest([
                'name' => '显影(预烤40min)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(预烤50min)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(预烤60min)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(预烤70min)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(预烤80min)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(预烤90min)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(预烤100min)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(预烤110min)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(预烤120min)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(放置12小时)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '显影(放置24小时)',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '表面张力',
                'data' => range(30, 42, 1),
            ]),
            new Suggest([
                'name' => '桥线',
                'data' => range(30, 200, 10),
            ]),
            new Suggest([
                'name' => '硬度',
                'data' => range(-7, 7, 1),
            ]),
            new Suggest([
                'name' => '附着力',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '耐焊性',
                'data' => ['PASS', '起泡掉油'],
            ]),
            new Suggest([
                'name' => '耐溶剂性',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '耐酸碱性',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '针孔测试',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '叠板',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '低能量显影不发白',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '隔夜曝光级数',
                'data' => range(0,24, 0.5),
            ]),
            new Suggest([
                'name' => '隔夜显影',
                'data' => ['干净','微量','少量','少量多','有残','严残','烤死一半','完全烤死'],
            ]),
            new Suggest([
                'name' => '解像性',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '去膜性',
                'data' => ['片状去膜', '溶解去膜', 'NG'],
            ]),
            new Suggest([
                'name' => '耐酸不发白',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '耐切削液',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '抗蚀刻性',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '耐电镀性',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '贮存寿命',
                'data' => null,
            ]),
            new Suggest([
                'name' => '有害物质含量限值',
                'data' => null,
            ]),
            new Suggest([
                'name' => '阻燃性',
                'data' => null,
            ]),
            new Suggest([
                'name' => '耐低高温循环',
                'data' => null,
            ]),
            new Suggest([
                'name' => '耐热冲击',
                'data' => null,
            ]),
            new Suggest([
                'name' => '绝缘电阻',
                'data' => null,
            ]),
            new Suggest([
                'name' => '溶解性',
                'data' => null,
            ]),
            new Suggest([
                'name' => '软化点',
                'data' => null,
            ]),
            new Suggest([
                'name' => '环氧值',
                'data' => null,
            ]),
            new Suggest([
                'name' => '挥发份',
                'data' => null,
            ]),
            new Suggest([
                'name' => '气味',
                'data' => null,
            ]),
            new Suggest([
                'name' => '吸油量',
                'data' => null,
            ]),
            new Suggest([
                'name' => '酸值',
                'data' => null,
            ]),
            new Suggest([
                'name' => '固含量',
                'data' => null,
            ]),
            new Suggest([
                'name' => '触变值',
                'data' => null,
            ]),
            new Suggest([
                'name' => '尺寸',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '重量',
                'data' => null,
            ]),
            new Suggest([
                'name' => '洁净度',
                'data' => null,
            ]),
            new Suggest([
                'name' => '做板',
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '抗摔强度', // 罐子
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '密封性', // 罐子
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '耐热性能', // 罐子
                'data' => ['PASS', 'NG'],
            ]),
            new Suggest([
                'name' => '缝合密合度', // 过滤丝网
                'data' => ['PASS', 'NG'],
            ]),
        ]);

        (new Suggest([
            'name' => '处理方法',
            'data' => [
                '特采',
                '返磨',
                '补加消泡剂',
                '补色浆',
                '补无色油',
                '兑油',
                '报废'
            ]
        ]))->save();
    }
}
