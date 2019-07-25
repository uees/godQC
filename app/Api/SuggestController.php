<?php

namespace App\Api;

use App\Http\Requests\SuggestRequest;
use App\Http\Resources\SuggestResource;
use App\Suggest;

class SuggestController extends Controller
{
    // parent_id, q, name, all
    public function index()
    {
        $query = Suggest::query();

        $this->parseWhere($query, ['parent_id', 'name']);

        if ($search = \request('q')) {
            $name_condition = queryCondition('name', $search);

            $query->where($name_condition);
        }

        $query->orderBy($this->sortBy(), $this->order());

        if (\request()->filled('all')) {
            $suggests = $query->get();
        } else {
            $suggests = $query->paginate($this->perPage())->appends(request()->except('page'));
        }

        return SuggestResource::collection($suggests);
    }

    public function store(SuggestRequest $request)
    {
        $this->authorize('create', Suggest::class);

        $data = $request->get('json_data');
        // 格式化数据
        if (!is_null($data)) {
            $data = is_array($data) ? $data : json_decode($data);
        }

        $suggest = new Suggest();
        $suggest->fill($request->only(['name', 'parent_id', 'memo']));
        $suggest->data = $data;
        $suggest->save();

        return SuggestResource::make($suggest);
    }

    public function show(Suggest $suggest)
    {
        return SuggestResource::make($suggest);
    }

    public function update(SuggestRequest $request, $id)
    {
        $suggest = Suggest::findOrFail($id);

        $this->authorize('update', $suggest);

        $suggest->fill($request->only(['name', 'parent_id', 'memo']));

        $data = $request->get('json_data');
        // 格式化数据
        if (!is_null($data)) {
            $data = is_array($data) ? $data : json_decode($data);
        }

        $suggest->data = $data;

        $suggest->save();

        return SuggestResource::make($suggest);
    }

    public function destroy($id)
    {
        $suggest = Suggest::findOrFail($id);

        $this->authorize('delete', $suggest);

        if ($suggest->delete()) {
            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }
}
