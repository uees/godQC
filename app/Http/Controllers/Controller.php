<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponseTrait;

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
            if (Cache::has($key)) {
                return $this->tooLarge();
            }

            if ($query->count() > $limit) {
                Cache::forever($key, date('Y-m-d H:i:s'));
                return $this->tooLarge();
            }
        }
    }
}
