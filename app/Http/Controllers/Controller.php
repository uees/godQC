<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponseTrait;

    /**
     * @return int
     */
    protected function perPage()
    {
        $perPage = (int)request('per_page', config('qc.perPage', 20));
        $maxPerPage = (int)config('qc.maxPerPage', 100);
        return $perPage <= $maxPerPage ? $perPage : $maxPerPage;
    }

    /**
     * @param array $limits
     * @param string $default
     * @return string
     */
    protected function sortBy(array $limits = null, $default = 'id')
    {
        $field = request('sort_by', $default);

        if (empty($limits) || in_array($field, $limits)) {
            return $field;
        }

        return $default;
    }

    /**
     * @param string $default
     * @return string
     */
    protected function order($default = 'desc')
    {
        $order = request('order', $default);

        if (in_array($order, ['asc', 'desc'])) {
            return $order;
        }

        return $default;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    protected function parseFields(Builder $query)
    {
        if (request()->filled('fields')) {
            $query->addSelect(explode(',', request('fields')));
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    protected function parseWhere(Builder $query, array $fields)
    {
        foreach ($fields as $field) {
            $value = request($field, '');
            if ($value == '') {
                continue;
            }

            if (preg_match('/^date:(\d{4}-\d{2}-\d{2}),?(\d{4}-\d{2}-\d{2})?$/', $value, $matches)) {
                if (count($matches) == 2) {
                    $min = $matches[1];
                    $query->where($field, '>', $min);
                } elseif (count($matches) == 3) {
                    $min = $matches[1];
                    $max = $matches[2];
                    $query->whereBetween($field, [$min, $max]);
                }
            } elseif (str_contains($value, ',')) {
                $query->whereIn($field, explode(',', $value));
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    protected function loadRelByQuery(Builder $query)
    {
        if (\request()->filled('with')) {
            $query->with(explode(',', request('with')));
        }

        return $query;
    }

    /**
     * @param Model $model
     * @return Model
     */
    protected function loadRelByModel(Model $model)
    {
        if (\request()->filled('with')) {
            $model->load(explode(',', request('with')));
        }

        return $model;
    }
}
