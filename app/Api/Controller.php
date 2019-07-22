<?php

namespace App\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var ApiResponse
     */
    protected $_response;

    /**
     * @return ApiResponse
     */
    protected function response()
    {
        if (is_null($this->_response)) {
            $this->_response = new ApiResponse;
        }

        return $this->_response;
    }

    /**
     * sql 语句黑名单检测机制检测机制
     *
     * @param $query
     * @param int $limit
     * @return \Illuminate\Http\JsonResponse
     */
    protected function checkBlacklist($query, $limit = 40)
    {
        if (is_null(request('per_page')) || (int)request('per_page') > $limit) {
            $key = 'sql:' . $query->toSql();
            if (\Cache::has($key)) {
                return $this->response()->tooLarge();
            }

            if ($query->count() > $limit) {
                \Cache::forever($key, date('Y-m-d H:i:s'));
                return $this->response()->tooLarge();
            }
        }
    }

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
