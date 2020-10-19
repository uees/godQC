<?php

namespace App;


trait RecordItemTrait
{
    /**
     * @param mixed|array $spec
     * @return mixed|string
     */
    public static function makeFakeValue($spec) {
        $fake_value = 'PASS';

        if (!is_array($spec) || empty($spec)) {
            return $fake_value;

        } elseif ($spec['value_type'] == 'INFO') {
            if (isset($spec['data']['value']) && $spec['data']['value']) {
                $tmpArr = explode('|', $spec['data']['value']);
                $fake_value = count($tmpArr) > 1 ? $tmpArr[1] : 'PASS';
            }
        } elseif ($spec['value_type'] == 'NUMBER') {
            $fake_value = isset($spec['data']['value']) && $spec['data']['value']
                ? $spec['data']['value']
                : 'PASS';

        } elseif ($spec['value_type'] == 'RANGE') {
            if (isset($spec['data']['max']) && $spec['data']['max']) {
                $fake_value = $spec['data']['max'];
            } elseif (isset($spec['data']['min']) && $spec['data']['min']) {
                $fake_value = $spec['data']['min'];
            }
        }

        return $fake_value;
    }
}