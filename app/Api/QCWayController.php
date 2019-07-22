<?php

namespace App\Api;

use App\Http\Requests\QCWayRequest;
use App\Http\Resources\TestWayResource;
use App\TestWay;

class QCWayController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();
        $query = TestWay::query();

        if ($search = \request('q')) {
            $name_condition = queryCondition('name', $search);

            $query = $query->where($name_condition);
        }

        $query->orderBy($this->sortBy(), $this->order());

        $pagination = $query->paginate($perPage)->appends(request()->except('page'));

        return TestWayResource::collection($pagination);
    }


    public function store(QCWayRequest $request)
    {
        $this->authorize('create', TestWay::class);

        $testWay = new TestWay();

        $testWay->fill($request->all())->save();

        return TestWayResource::make($testWay);
    }


    public function show(TestWay $testWay)
    {
        return TestWayResource::make($testWay);
    }


    public function update(QCWayRequest $request, $id)
    {
        $testWay = TestWay::findOrFail($id);

        $this->authorize('update', $testWay);

        $testWay->fill($request->all())->save();

        return TestWayResource::make($testWay);
    }


    public function destroy($id)
    {
        $testWay = TestWay::findOrFail($id);

        $this->authorize('delete', $testWay);

        if ($testWay->delete()) {
            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }
}
