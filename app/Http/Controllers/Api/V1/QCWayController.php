<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QCWayRequest;
use App\Http\Resources\TestWayResource;
use App\TestWay;

class QCWayController extends Controller
{
    public function index()
    {
        $name_condition = queryCondition('name', \request('q'));

        $ways = TestWay::where($name_condition)->get(); // 直接获取所有

        return TestWayResource::collection($ways);
    }


    public function store(QCWayRequest $request)
    {
        $testWay = new TestWay();

        $testWay->fill($request->all())->save();

        return TestWayResource::make($testWay);
    }


    public function show(TestWay $testWay)
    {
        return TestWayResource::make($testWay);
    }


    public function update(QCWayRequest $request, TestWay $testWay)
    {
        $testWay->fill($request->all())->save();

        return TestWayResource::make($testWay);
    }


    public function destroy(TestWay $testWay)
    {
        $testWay->delete();

        return $this->noContent();
    }
}
