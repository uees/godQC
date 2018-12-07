<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuggestRequest;
use App\Http\Resources\SuggestResource;
use App\Suggest;

class SuggestController extends Controller
{
    // parent_id, q, name
    public function index()
    {
        $query = Suggest::query();

        $query = $this->parseWhere($query, ['parent_id', 'name']);

        if (\request()->filled('q')) {
            $name_condition = queryCondition('name', \request('q'));

            $query = $query->where($name_condition);
        }

        $query = $query->orderBy($this->sortBy(), $this->order());

        $suggests = $query->get();

        return SuggestResource::collection($suggests);
    }

    public function store(SuggestRequest $request)
    {
        $suggest = new Suggest();
        $suggest->fill($request->all())->save();

        return SuggestResource::make($suggest);
    }

    public function show(Suggest $suggest)
    {
        return SuggestResource::make($suggest);
    }

    public function update(SuggestRequest $request, $id)
    {
        $suggest = Suggest::findOrFail($id);
        $suggest->fill($request->all())->save();

        return SuggestResource::make($suggest);
    }

    public function destroy($id)
    {
        if (Suggest::destroy($id)) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
