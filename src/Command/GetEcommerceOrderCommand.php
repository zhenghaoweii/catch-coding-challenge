<?php

namespace App\Command;

use App\Pipeline\EcommerceOrder\ExcludeZeroOrderValue;
use App\Pipeline\EcommerceOrder\ExportData;
use App\Pipeline\EcommerceOrder\ProcessingData;
use App\Pipeline\EcommerceOrder\SerializeJsonl;
use League\Pipeline\Pipeline;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GetEcommerceOrderCommand extends Command
{
    protected static $defaultName = 'catch:export-orders';
    protected static $defaultDescription = 'Export ecommerce orders';

    protected function configure(): void
    {
        $this
                ->addOption(
                        'type',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Export to CSV|jsonl|xml|yaml|xsl. Default CSV'
                );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // default type is 'csv';
        $type = in_array(strtolower($input->getOption('type')), ['jsonl', 'xlsx', 'xls', 'yaml', 'xml', 'csv']) ? $input->getOption('type') : 'csv';

        $filesystem = new Filesystem();
        $filesystem->remove('public/out.'.strtolower($type));

        $file = file_get_contents('https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl');

        (new Pipeline())
                ->pipe(new SerializeJsonl())
                ->pipe(new ExcludeZeroOrderValue())
                ->pipe(new ProcessingData())
                ->pipe(new ExportData())
                ->process((object) [
                        'file' => $file,
                        'type' => $type,
                ]);

        $output->writeln('Exported ecommerce order successfully!');
        $output->writeln('you can download the file on /out.'.$type);

        return Command::SUCCESS;
    }
}
