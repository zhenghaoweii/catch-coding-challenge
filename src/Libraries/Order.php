<?php

namespace App\Libraries;

class Order
{
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function getTotalUnits()
    {
        return collect($this->order['items'])->pluck('quantity')->sum();
    }

    public function getTotalUniqueUnits()
    {
        return collect($this->order['items'])->map(function ($item) {
            return $item['product']['product_id'];
        })->unique()->count();
    }

    public function getAverageUnitPrice()
    {
        return $this->getTotalOrderValue() / $this->getTotalUnits();
    }

    public function getTotalOrderValue()
    {
        $order = $this->order;
        $totalOrderValue = collect($order['items'])->map(function ($item) {
            return [
                    'orderValue' => $item['quantity'] * $item['unit_price'],
            ];
        })->sum('orderValue');

        // if discounts is applied
        if (!empty($order['discounts'])) {
            // get top discount priority
            $discount = collect($order['discounts'])->sortBy('priority')->first();

            switch ($discount['type']) {
                case 'PERCENTAGE':
                    $discountedValue = $totalOrderValue * ($discount['value'] / 100);
                    $totalOrderValue = $totalOrderValue - $discountedValue;
                    break;
                default:
                    $totalOrderValue = $totalOrderValue - $discount['value'];
            }
        }

        return $totalOrderValue;
    }
}
