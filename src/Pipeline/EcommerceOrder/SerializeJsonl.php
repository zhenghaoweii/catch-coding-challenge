<?php


namespace App\Pipeline\EcommerceOrder;


class SerializeJsonl
{
    public function __invoke($payload)
    {
        $json = json_decode(json_encode(explode("\n", $payload->file)), true);

        $payload->data = collect($json)
                // json_decode
                ->map(function ($item) {
                    return json_decode($item, true);
                })->all();

        return $payload;
    }
}