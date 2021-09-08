<?php


namespace App\Pipeline\EcommerceOrder;


use League\Csv\Writer;

class ExportData
{
    public function __invoke($payload)
    {
        //load the CSV document from a string
        $csv = Writer::createFromString();
        //insert the header
        $csv->insertOne(['Order ID', 'Order Date', 'Total Order value', 'Average Unit Price', 'Distinct Unit Count', 'Total Units Count']);
        //insert all the records
        $csv->insertAll($payload->data);

        $file = "public/out.csv";

        $txt = fopen($file, "w") or die("Unable to open file!");
        fwrite($txt, $csv->toString());
    }
}