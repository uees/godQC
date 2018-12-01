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
        $query = TestWay::query();

        if (\request()->filled('q')) {
            $name_condition = queryCondition('name', \request('q'));
            $query=$query->where($name_condition);
        }

        $perPage = $this->perPage();

        $pagination = $query->paginate($perPage)->appends(request()->except('page'));

        return TestWayResource::collection($pagination);
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
