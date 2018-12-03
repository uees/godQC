<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponseTrait;

    protected function perPage()
    {
        $perPage = request('per_page', config('qc.perPage', 20));
        $maxPerPage = config('qc.maxPerPage', 40);
        return $perPage <= $maxPerPage ? $perPage : $maxPerPage;
    }

    protected function sortBy()
    {
        return request('sort_by', 'id');
    }

    protected function order()
    {
        return request('order', 'desc');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    protected function parseFields(Builder $query)
    {
        if (request()->filled('fields')) {
            $query = $query->addSelect(explode(',', request('fields')));
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
            $value = request($field);
            if (is_null($value)) {
                continue;
            }

            if (preg_match('/^date:(\d{4}-\d{2}-\d{2}),?(\d{4}-\d{2}-\d{2})?$/', $value, $matches)) {
                if (count($matches) == 2) {
                    $min = $matches[1];
                    $query = $query->where($field, '>', $min);
                } elseif (count($matches) == 3) {
                    $min = $matches[1];
                    $max = $matches[2];
                    $query = $query->whereBetween($field, [$min, $max]);
                }
            } elseif (str_contains($value, ',')) {
                $query = $query->whereIn($field, explode(',', $value));
            } else {
                $query = $query->where($field, $value);
            }
        }

        return $query;
    }
}
