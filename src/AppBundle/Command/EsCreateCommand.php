<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Greet someone')
            ->addArgument(
                'number',
                InputArgument::OPTIONAL,
                'How manu users do you mant to create?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $n = 1;
        $number = $input->getArgument('number');
        if ($number && is_numeric($number)) {
            $number = intval($number);
            if ($number < 0 or $number > 1000) {
                $output->writeln("number should be between 1 and 1000");
                return;
            }
            $n = $number;
        }

        $faker = Faker\Factory::create();
        $faker->addProvider(new Faker\Provider\nl_NL\Person($faker));
        $faker->addProvider(new Faker\Provider\Lorem($faker));

        $categories = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank");
        for ($x = 0; $x < $n; $x++) {
            $item = new Item();
            $item->name = $faker->name();
            $item->text = $faker->paragraph(5);
            $nn = rand(1, 4);
            $cats = array_rand(array_flip($categories), $nn);

            if (!is_array($cats)) {
                $cats = [$cats];
            }
            foreach ($cats as $cat) {
                $item->categories[] = new Category($cat);
            }


            $item->location = $faker->latitude(51,52).",".$faker->longitude(4,5);

            $this->manager->persist($item);
            $this->manager->commit();

            $output->writeln(sprintf("User '%s' created", $item->name));

        }

    }
}