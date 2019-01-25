<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatternTestResource;
use App\PatternTest;
use Illuminate\Http\Request;

class PatternTestController extends Controller
{
    // q, all
    public function index()
    {
        $query = PatternTest::query();

        if ($search = \request('q')) {
            $query->where(function ($query) use ($search) {
                $product_name_condition = queryCondition('product_name', $search);
                $batch_number_condition = queryCondition('batch_number', $search);

                $query->where($product_name_condition)
                    ->orWhere($batch_number_condition);
            });
        }

        $query->orderBy($this->sortBy(), $this->order());

        if (\request()->filled('all')) {
            $suggests = $query->get();
        } else {
            $suggests = $query->paginate($this->perPage())->appends(request()->except('page'));
        }

        return PatternTestResource::collection($suggests);
    }

    public function store(Request $request)
    {
        $this->authorize('create', PatternTest::class);

        $test = new PatternTest();
        $test->fill($request->all());
        $test->save();

        return PatternTestResource::make($test);
    }

    public function show(PatternTest $test)
    {
        return PatternTestResource::make($test);
    }

    public function update(Request $request, $id)
    {
        $test = PatternTest::findOrFail($id);

        $this->authorize('update', $test);

        $test->fill($request->all());

        $test->save();

        return PatternTestResource::make($test);
    }

    public function destroy($id)
    {
        $patternTest = PatternTest::findOrFail($id);

        $this->authorize('delete', $patternTest);

        if ($patternTest->delete()) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
