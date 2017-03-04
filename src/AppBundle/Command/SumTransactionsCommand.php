<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use \DateTime;
use \DateInterval;
// Add the Container
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SumTransactionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:sum-transactions')
            ->setDescription('Sum transactions previos days')
            ->setHelp('This command allows sum transactions')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('date', 'd', InputOption::VALUE_OPTIONAL, "Date for sum transactions"),
                ))
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');

        $date = $input->getOption('date');

        if (empty($date)) {
            $date = new DateTime();
            $date->add(DateInterval::createFromDateString('yesterday'));
        }
        $doctrine->getRepository('AppBundle:Transaction')->sumTransactions($date);
    }
}