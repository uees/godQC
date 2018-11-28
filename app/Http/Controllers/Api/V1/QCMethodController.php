<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QCMethodRequest;
use App\Http\Resources\TestMethodResource;
use App\TestMethod;

class QCMethodController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();
        $name_condition = queryCondition('name', \request('q'));
        $content_condition = queryCondition('content', \request('q'));

        $pagination = TestMethod::where($name_condition)
            ->orWhere($content_condition)
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return TestMethodResource::collection($pagination);
    }


    public function store(QCMethodRequest $request)
    {
        $testMethod = new TestMethod();

        $testMethod->fill($request->all())->save();

        return TestMethodResource::make($testMethod);
    }


    public function show(TestMethod $testMethod)
    {
        return TestMethodResource::make($testMethod);
    }


    public function update(QCMethodRequest $request, TestMethod $testMethod)
    {
        $testMethod->fill($request->all())->save();

        return TestMethodResource::make($testMethod);
    }


    public function destroy(TestMethod $testMethod)
    {
        $testMethod->delete();

        return $this->noContent();
    }
}
