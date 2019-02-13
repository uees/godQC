<?php

namespace App\Http\Controllers\Api\V1;

use App\ProductBatch;
use App\TestRecord;
use App\Category;
use App\TestStatistics;
use App\DisqualificationStatistics;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class StatisticsController extends Controller
{
    public function makeTestStatistics($type, $year, $month)
    {
        // 清除旧数据
        TestStatistics::query()
            ->where('qc_type', $type)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->delete();

        $s = new TestStatistics([
            'year' => $year,
            'month' => $month,
            'qc_type' => $type,
            'tests_num' => $this->getTestsNum($year, $month, $type),
            'once_disqualification_num' => $this->getOnceDisqualificationNum($year, $month, $type),
            'disqualification_num' => $this->getDisqualificationNum($year, $month, $type),
            'force_accept_num' => $this->getForceAcceptNum($year, $month, $type)
        ]);
        $s->save();


        foreach (Category::query()->get() as $category) {
            $s = new TestStatistics([
                'year' => $year,
                'month' => $month,
                'qc_type' => $type,
                'tests_num' => $this->getTestsNum($year, $month, $type, $category->id),
                'once_disqualification_num' => $this->getOnceDisqualificationNum($year, $month, $type, $category->id),
                'disqualification_num' => $this->getDisqualificationNum($year, $month, $type, $category->id),
                'force_accept_num' => $this->getForceAcceptNum($year, $month, $type, $category->id)
            ]);
            $s->category()->associate($category);
            $s->save();
        }

        return $this->message('success');
    }

    public function makeDisqualificationStatistics($type, $year, $month)
    {
        // 获取所有不合格
        $failedRecords = TestRecord::with('items')
            ->whereHas('batch', function ($query) use ($type) {
                $query->where('type', $type);
            })->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->doesntHave('disposed')
            ->where('conclusion', 'NG')->get();

        $data = []; // item => amount
        foreach ($failedRecords as $record) {
            $items = $record->items->where('conclusion', 'NG')->all();

            foreach ($items as $item) {
                if (isset($data[$item['item']])) {
                    $data[$item['item']] += 1;
                } else {
                    $data[$item['item']] = 1;
                }
            }
        }

        // 清除旧数据
        DisqualificationStatistics::query()
            ->where('qc_type', $type)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->delete();

        // 保持数据
        $statistics = $this->toStatistics($data, $year, $month, $type);
        \DB::table('disqualification_statistics')->insert($statistics);

        return $this->message('success');
    }

    protected function toStatistics($data, $year, $month, $type)
    {
        $total = 0;
        foreach ($data as $amount) {
            $total += $amount;
        }

        $statistics = [];
        foreach ($data as $item => $amount) {
            array_push($statistics, [
                'item' => $item,
                'amount' => $amount,
                'year' => $year,
                'month' => $month,
                'qc_type' => $type,
                'rate' => $amount / $total,
            ]);
        }

        return $statistics;
    }

    protected function getTestsNum($year, $month, $type = 'FQC', $categoryId = null)
    {
        $query = ProductBatch::query()->where('type', $type)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->has('testRecords');

        if ($categoryId) {
            if ($category = Category::where('id', $categoryId)->first()) {
                $products = $category->products->pluck('internal_name');
                $query->whereIn('product_name', $products);
            }
        }

        return $query->count();
    }

    protected function getOnceDisqualificationNum($year, $month, $type = 'FQC', $categoryId = null)
    {
        return TestRecord::query()
            ->whereHas('batch', function ($query) use ($type, $categoryId) {
                $query->where('type', $type);
                if ($categoryId) {
                    if ($category = Category::where('id', $categoryId)->first()) {
                        $products = $category->products->pluck('internal_name');
                        $query->whereIn('product_name', $products);
                    }
                }
            })->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->doesntHave('disposed')
            ->where('conclusion', 'NG')
            ->count();
    }

    protected function getDisqualificationNum($year, $month, $type = 'FQC', $categoryId = null)
    {
        return TestRecord::query()
            ->whereHas('batch', function ($query) use ($type, $categoryId) {
                $query->where('type', $type);
                if ($categoryId) {
                    if ($category = Category::where('id', $categoryId)->first()) {
                        $products = $category->products->pluck('internal_name');
                        $query->whereIn('product_name', $products);
                    }
                }
            })->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('conclusion', 'NG')
            ->where(function ($query) {
                $query->doesntHave('willDispose')
                    ->orWhereHas('willDispose', function ($query) {
                        $query->where('method', '不合格')
                            ->orWhere('method', '无法处理')
                            ->orWhere('method', '报废');
                    });
            })
            ->count();
    }

    public function getForceAcceptNum($year, $month, $type = 'FQC', $categoryId = null)
    {
        return TestRecord::query()
            ->whereHas('batch', function ($query) use ($type, $categoryId) {
                $query->where('type', $type);
                if ($categoryId) {
                    if ($category = Category::where('id', $categoryId)->first()) {
                        $products = $category->products->pluck('internal_name');
                        $query->whereIn('product_name', $products);
                    }
                }
            })->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('conclusion', 'NG')
            ->WhereHas('willDispose', function ($query) {
                $query->where('method', '特采');
            })
            ->count();
    }
}
