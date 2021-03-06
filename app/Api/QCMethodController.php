<?php

namespace App\Api;

use App\Http\Requests\QCMethodRequest;
use App\Http\Resources\TestMethodResource;
use App\TestMethod;

class QCMethodController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();

        $query = TestMethod::query();

        if ($search = \request('q')) {
            $query->where(function ($query) use ($search) {
                $name_condition = queryCondition('name', $search);
                $content_condition = queryCondition('content', $search);

                $query->where($name_condition)
                    ->orWhere($content_condition);
            });
        }

        $query->orderBy($this->sortBy(), $this->order());

        $pagination = $query->paginate($perPage)->appends(request()->except('page'));

        return TestMethodResource::collection($pagination);
    }


    public function store(QCMethodRequest $request)
    {
        $this->authorize('create', TestMethod::class);

        $testMethod = new TestMethod();

        $testMethod->fill($request->all())->save();

        return TestMethodResource::make($testMethod);
    }


    public function show(TestMethod $testMethod)
    {
        return TestMethodResource::make($testMethod);
    }


    public function update(QCMethodRequest $request, $id)
    {
        $testMethod = TestMethod::findOrFail($id);

        $this->authorize('update', $testMethod);

        $testMethod->fill($request->all())->save();

        return TestMethodResource::make($testMethod);
    }


    public function destroy($id)
    {
        $testMethod = TestMethod::findOrFail($id);

        $this->authorize('delete', $testMethod);

        if ($testMethod->delete()) {
            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }
}
