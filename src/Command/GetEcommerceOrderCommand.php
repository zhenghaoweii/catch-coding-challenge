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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class GetEcommerceOrderCommand extends Command
{
    protected static $defaultName = 'catch:export-orders';
    protected static $defaultDescription = 'Export ecommerce orders';

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;

        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
                ->addOption(
                        'email',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Sent the result to email'
                )
                ->addOption(
                        'type',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Export to CSV|jsonl|xml|yaml|xsl. Default CSV'
                );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getOption('email');
        $type = in_array(strtolower($input->getOption('type')),
                ['jsonl', 'xlsx', 'xls', 'yaml', 'xml', 'csv']) ? $input->getOption('type') : 'csv';

        $filesystem = new Filesystem();
        $filesystem->remove('public/out.'.strtolower($type));

        // read the json file
        $file = file_get_contents('https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl');

        // execution
        (new Pipeline())
                ->pipe(new SerializeJsonl())
                ->pipe(new ExcludeZeroOrderValue())
                ->pipe(new ProcessingData())
                ->pipe(new ExportData())
                ->process((object) [
                        'file' => $file,
                        'type' => $type,
                ]);

        $output->writeln('');
        $output->writeln('<info>Completed!</info>');

        if ($email) {
            $output->writeln('<info>Sending the output to '.$email.'</info>');
            $this->sendEmail($email, $type);
            $output->writeln('<info>Please check your inbox!</info>');
            $output->writeln('<info>You can download the file on public/out.'.$type.' as well</info>');
        } else {
            $output->writeln('<info>You can download the file on public/out.'.$type.'</info>');
        }

        return Command::SUCCESS;
    }

    private function sendEmail($email, $type)
    {
        $sendEmail = (new Email())
                ->from('hello@example.com')
                ->to($email)
                ->attachFromPath('public/out.'.$type)
                ->subject('Ecommerce Orders report')
                ->html('<p>Attached is the result of the output </p>');

        $this->mailer->send($sendEmail);
    }
}
