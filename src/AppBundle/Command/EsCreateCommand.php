<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ONGR\ElasticsearchBundle\Service\Manager;
use AppBundle\Document\Item;
use AppBundle\Document\Day;
use AppBundle\Document\Category;
use Faker;


class EsCreateCommand extends ContainerAwareCommand
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var array
     */
    private $weekdays;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var array
     */
    private $filters;

    public function __construct(
        Manager $manager,
        array $weekdays,
        array $categories,
        array $filters
    )
    {
        parent::__construct();

        $this->manager = $manager;
        $this->weekdays = array_keys(array_flip($weekdays));
        $this->categories = array_keys(array_flip($categories));
        $this->filters = array_keys(array_flip($filters));
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

        for ($x = 0; $x < $nItems; $x++) {
            $item = new Item();

            $item->name = $faker->name();
            $item->text = $faker->paragraph(5);
            $item->location = $faker->latitude(51, 52) . "," . $faker->longitude(4, 5);

            $item->filter = array_rand(array_flip($this->filters),1);

            $cats = array_rand(array_flip($this->categories), rand(1, 4));
            foreach ((array)$cats as $cat) {
                $item->categories[] = new Category($cat);
            }

            $days = array_rand(array_flip($this->weekdays), rand(1, 6));
            foreach ((array)$days as $day) {
                $item->days[] = new Day($day);
            }

            $this->manager->persist($item);
            $this->manager->commit();

            $output->writeln(sprintf("User '%s' created", $item->name));
        }
    }
}