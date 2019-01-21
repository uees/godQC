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
        $maxPerPage = config('qc.maxPerPage', 100);
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
     */
    protected function parseFields(Builder $query)
    {
        if (request()->filled('fields')) {
            $query->addSelect(explode(',', request('fields')));
        }
    }

    /**
     * @param Builder $query
     * @param array $fields
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
    }
}
