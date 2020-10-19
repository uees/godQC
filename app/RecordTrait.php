<?php

namespace App;


trait RecordTrait
{
    /**
     * @param array $items
     */
    public function UpdateItems(array $items)
    {
        foreach ($items as $item) {
            $this->items()
                ->where('id', $item['id'])
                ->update([
                    'value' => $item['value'],
                    'fake_value' => $item['fake_value'],
                    'tester' => $item['tester'],
                    'conclusion' => $item['conclusion'],
                    'memo' => $item['memo'],
                ]);
        }
    }
}