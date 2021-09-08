<?php

namespace App\Pipeline\EcommerceOrder;

use App\Libraries\Order;

class ProcessingData
{
    public function __invoke($payload)
    {
        $payload->data = collect($payload->data)
                ->map(function ($item) {
                    $order = (new Order($item));
                    $data = [
                            'order_id' => $item['order_id'],
                            'order_datetime' => $item['order_date'],
                            'total_order_value' => $order->getTotalOrderValue(),
                            'average_unit_price' => $order->getAverageUnitPrice(),
                            'distinct_unit_count' => $order->getTotalUniqueUnits(),
                            'total_units_count' => $order->getTotalUnits(),
                    ];

                    return $data;
                })->all();

        return $payload;
    }
}
