<?php

namespace App\Pipeline\EcommerceOrder;

use League\Csv\Writer;
use Symfony\Component\Yaml\Yaml;

class ExportData
{
    public function __invoke($payload)
    {
        $type = strtolower($payload->type);
        switch ($type) {
            case 'xlsx':
            case 'xls':
                $this->xlsx($payload);
                break;
            default:
              $this->{$type}($payload);
        }
    }

    protected function xlsx($payload)
    {
        $header = ['Order ID', 'Order Date', 'Total Order value', 'Average Unit Price', 'Distinct Unit Count', 'Total Units Count'];
        $xslx = collect($payload->data)->map(function ($item) {
            return collect($item)->values()->all();
        })->prepend($header)->all();
        \SimpleXLSXGen::fromArray($xslx)->saveAs('public/out.xlsx');
    }

    protected function jsonl($payload)
    {
        $jsonl = collect($payload->data)->map(function ($item) {
            return json_encode($item);
        })->values()->implode("\n");
        $file = 'public/out.jsonl';
        $txt = fopen($file, 'w') or exit('Unable to open file!');
        fwrite($txt, $jsonl);
    }

    protected function yaml($payload)
    {
        $yaml = Yaml::dump($payload->data);
        file_put_contents('public/out.yaml', $yaml);
    }

    protected function xml($payload)
    {
        $xml = '<root_contact>';

        foreach ($payload->data as $r) {
            $xml .= '<contact order_id="'.$r['order_id'].'"
                                    order_date="'.$r['order_datetime'].'"
                                    total_order_value="'.$r['total_order_value'].'"
                                    average_unit_price="'.$r['average_unit_price'].'"
                                    distinct_unit_count="'.$r['distinct_unit_count'].'"
                                    total_units_count="'.$r['total_units_count'].'"
                            />';
        }

        $xml .= '</root_contact>';

        $sxe = new \SimpleXMLElement($xml);
        $dom = new \DOMDocument('1,0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($sxe->asXML());
        $dom->saveXML();
        $dom->save('public/out.xml');
    }

    protected function csv($payload)
    {
        $csv = Writer::createFromString();
        $csv->insertOne(['Order ID', 'Order Date', 'Total Order value', 'Average Unit Price', 'Distinct Unit Count', 'Total Units Count']);
        $csv->insertAll($payload->data);

        $file = 'public/out.csv';

        $txt = fopen($file, 'w') or exit('Unable to open file!');
        fwrite($txt, $csv->toString());
    }
}
