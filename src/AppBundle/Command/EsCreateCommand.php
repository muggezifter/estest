<?php

namespace AppBundle\Command;

use AppBundle\Service\Creator;
use Faker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class EsCreateCommand extends Command
{
    /**
     * @var Creator
     */
    private $creator;
    /**
     * Construct a new EsCreateCommand.
     *
     * @param Creator $creator
     */
    public function __construct(Creator $creator)
    {
        parent::__construct();

        $this->creator = $creator;
    }

    /**
     * Configure.
     */
    protected function configure()
    {
        $this
            ->setName('estest:create')
            ->setDescription('create items')
            ->addArgument(
                'number',
                InputArgument::OPTIONAL,
                'How many items do you want to create?'
            );
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $number = $input->getArgument('number') ?: 1;

        switch ($number) {
            case (!is_numeric($number)):
                $output->writeln("number should be a number");
                break;
            case ($number < 0 || $number > 1000):
                $output->writeln("number should be between 1 and 1000");
                break;
            default:
                $this->creator->createItems($number, $output);
        }
    }
}