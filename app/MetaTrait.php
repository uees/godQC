<?php

namespace App;

trait MetaTrait
{
    /**
     * get user metas
     * @param $value
     * @return mixed
     */
    public function getMetasAttribute($value)
    {
        return \json_decode($value);
    }

    /**
     * @param $value
     */
    public function setMetasAttribute($value)
    {
        $this->attributes['metas'] = \json_encode($value);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function meta($key, $default = null)
    {
        return $this->metas->{$key} ?? $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setMeta($key, $value)
    {
        // fix Creating default object from empty value
        $metas = $this->metas ?? new \stdClass();

        $metas->{$key} = $value;

        $this->metas = $metas;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int|null $limit
     */
    public function pushMeta($key, $value, $limit = null)
    {
        $v = (array)($this->metas->{$key} ?? []);

        // 如果是非数值数组 则是一个项目
        if (!empty(array_diff_assoc(array_keys($v), range(0, sizeof($v))))) {
            $v = [$v];
        }

        if (isset($limit) && count($v) >= $limit) {
            array_shift($v);  // 移除第一个
        }

        array_push($v, $value);

        $this->setMeta($key, $v);
    }
}
