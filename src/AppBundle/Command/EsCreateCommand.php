<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ONGR\ElasticsearchBundle\Service\Manager;
use AppBundle\Document\Item;
use AppBundle\Document\Category;
use Faker;


class EsCreateCommand extends ContainerAwareCommand
{
    private $manager;

    public function __construct(Manager $manager)
    {
        parent::__construct();
        $this->manager = $manager;

    }

    protected function configure()
    {
        $this
            ->setName('estest:create')
            ->setDescription('create items')
            ->addArgument(
                'number',
                InputArgument::OPTIONAL,
                'How many items do you mant to create?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nItems = 1;
        $number = $input->getArgument('number');
        if ($number && is_numeric($number)) {
            $number = intval($number);
            if ($number < 0 || $number > 1000) {
                $output->writeln("number should be between 1 and 1000");
                return;
            }
            $nItems = $number;
        }

        $faker = Faker\Factory::create();
        $faker->addProvider(new Faker\Provider\nl_NL\Person($faker));
        $faker->addProvider(new Faker\Provider\Lorem($faker));

        $categories = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank");
        for ($x = 0; $x < $nItems; $x++) {
            $item = new Item();

            $item->name = $faker->name();
            $item->text = $faker->paragraph(5);
            $item->location = $faker->latitude(51, 52) . "," . $faker->longitude(4, 5);

            $cats = array_rand(array_flip($categories), rand(1, 4));
            foreach ((array)$cats as $cat) {
                $item->categories[] = new Category($cat);
            }

            $this->manager->persist($item);
            $this->manager->commit();

            $output->writeln(sprintf("User '%s' created", $item->name));
        }
    }
}