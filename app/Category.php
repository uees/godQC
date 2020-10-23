<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use MetaTrait;
    /**
     * metas => [
     *      ‘templates’ => [
     *          0 => [
     *              'name' => 'pdf',  // ‘pdf’ 或者 doc 模板文件名称
     *              'tips' => [],   // tips 是列表
     *              'options' => [  // options 是字典
     *                  'templates_dir' => '',  // 如果 name 是 pdf , 那么这里定义 pdf 模板路径
     *                  ‘customers’ => '深南',
     *              ],
     *          ],
     *      ]
     * ]
     *
     */

    protected $fillable = [
        'test_way_id',
        'name',
        'slug',
        'memo',
        'metas',
    ];

    /**
     * Get the products for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::Class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testWay()
    {
        return $this->belongsTo(TestWay::class);
    }
}
