<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use App\TestRecord;
use App\MixinTestRecord;

class Fix8BKT11 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:8bkt11';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新8BKT11粘度';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 修复检测记录
        $records = TestRecord::query()->whereHas('batch', function (Builder $query) {
            $query->where('product_name', '8BKT11');
        })->get();

        foreach ($records as $record) {
            foreach ($record->items as $item) {
                if ($item->item == "混合粘度") {
                    $spec = $item->spec;
                    $spec["value_type"] = "INFO";
                    $spec["data"]["value"] = "130±50";
                    $item->spec = $spec;
                    $item->fake_value = random_int(120, 160);
                    $item->value = $item->fake_value;
                    $item->save();
                }
            }
        }

        // 修复混合检测记录
        $records = MixinTestRecord::query()->whereHas('product', function (Builder $query) {
            $query->where('internal_name', '8BKT11');
        })->get();

        foreach ($records as $record) {
            foreach ($record->items as $item) {
                if ($item->item == "混合粘度") {
                    if ((int)($item->value) > 180) {
                        $item->fake_value = 180;
                        $item->value = 180;
                        $item->save();
                    }
                }
            }
        }
    }
}
