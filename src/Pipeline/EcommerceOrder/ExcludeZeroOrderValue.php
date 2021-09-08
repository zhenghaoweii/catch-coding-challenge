<?php


namespace App\Pipeline\EcommerceOrder;


class ExcludeZeroOrderValue
{
    public function __invoke($payload)
    {
        $payload->data = collect($payload->data)
                // exlcude 0 total order value
                ->filter(function ($item) {
                    return (isset($item['items'])) && collect($item['items'])->sum('quantity') > 0;
                })->all();

        return $payload;
    }
}