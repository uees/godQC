<?php


namespace App\Api;

use App\Http\Resources\A9060PatternTestResource;
use App\A9060PatternTest;
use Illuminate\Http\Request;


class A9060PatternTestController extends Controller
{
    // q, all
    public function index()
    {
        $query = A9060PatternTest::query();

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

        return A9060PatternTestResource::collection($suggests);
    }

    public function store(Request $request)
    {
        $this->authorize('create', A9060PatternTest::class);

        $test = new A9060PatternTest();
        $test->fill($request->all());
        $test->save();

        return A9060PatternTestResource::make($test);
    }

    public function show(A9060PatternTest $test)
    {
        return A9060PatternTestResource::make($test);
    }

    public function update(Request $request, $id)
    {
        $test = A9060PatternTest::findOrFail($id);

        $this->authorize('update', $test);

        $test->fill($request->all());

        $test->save();

        return A9060PatternTestResource::make($test);
    }

    public function destroy($id)
    {
        $patternTest = A9060PatternTest::findOrFail($id);

        $this->authorize('delete', $patternTest);

        if ($patternTest->delete()) {
            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }
}