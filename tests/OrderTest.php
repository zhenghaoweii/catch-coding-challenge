<?php

namespace App\Tests;

use App\Libraries\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testGetTotalUnits()
    : void
    {
        $data = $this->getInitializeData();
        $this->assertEquals(10, (new Order($data))->getTotalUnits());
    }

    public function testGetTotalUniqueUnits()
    : void
    {
        $data = $this->getInitializeData();
        $this->assertEquals(2, (new Order($data))->getTotalUniqueUnits());
    }

    public function testGetTotalOrderValue()
    : void
    {
        $data = $this->getInitializeData();
        $this->assertEquals(694, (new Order($data))->getTotalOrderValue());
    }

    public function testGetAverageUnitPrice()
    : void
    {
        $data = $this->getInitializeData();
        $this->assertEquals(69.4, (new Order($data))->getAverageUnitPrice());
    }


    public function testGetTotalOrderValueWithDiscountInDollar()
    : void
    {
        $data              = $this->getInitializeData();
        $data["discounts"] = [
                ["type" => "DOLLAR", "value" => 10, "priority" => 1],
                ["type" => "PERCENTAGE", "value" => 8, "priority" => 2],
        ];
        $this->assertEquals( 690, (new Order($data))->getTotalOrderValue());
    }



    public function testGetTotalOrderValueWithDiscountInPercentage()
    : void
    {
        $data              = $this->getInitializeData();
        $data["discounts"] = [
                ["type" => "DOLLAR", "value" => 10, "priority" => 2],
                ["type" => "PERCENTAGE", "value" => 8, "priority" => 1],
        ];

        $this->assertEquals( 644, (new Order($data))->getTotalOrderValue());
    }

    protected function getInitializeData()
    {
        return [
                "order_id"       => 1001,
                "order_date"     => "Fri, 08 Mar 2019 12:13:29 +0000",
                "items"          => [
                        [
                                "quantity"   => 4,
                                "unit_price" => 100,
                                "product"    => [
                                        "product_id" => 3793908,
                                        "title"      => "Cellsafe Radi Chip Universal",
                                ]
                        ],
                        [
                                "quantity"   => 6,
                                "unit_price" => 50,
                                "product"    => [
                                        "product_id" => 3879592,
                                        "title"      => "Pulsar Men's 41mm Day Stainless Steel Watch - Gold",
                                ]
                        ]
                ],
                "discounts"      => [
                        ["type" => "DOLLAR", "value" => 6, "priority" => 1],
                        ["type" => "PERCENTAGE", "value" => 8, "priority" => 2],
                ],
                "shipping_price" => 6.99,
        ];
    }
}
