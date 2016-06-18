<?php

namespace AppBundle\Command;

use AppBundle\Document\Category;
use AppBundle\Document\Day;
use AppBundle\Document\Item;
use Faker;
use Faker\Factory;
use Faker\Provider\Lorem;
use Faker\Provider\nl_NL\Person;
use ONGR\ElasticsearchBundle\Service\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class EsCreateCommand extends Command
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
    /**
     * @var array
     */
    private $range;
    /**
     * @var Faker
     */
    private $faker;

    /**
     * Construct a new EsCreateCommand.
     *
     * @param Manager $manager
     * @param array $weekdays
     * @param array $categories
     * @param array $filters
     * @param array $range
     */
    public function __construct(
        Manager $manager,
        array $weekdays,
        array $categories,
        array $filters,
        array $range
    )
    {
        parent::__construct();

        $this->manager = $manager;
        $this->weekdays = array_keys(array_flip($weekdays));
        $this->categories = array_keys(array_flip($categories));
        $this->filters = array_keys(array_flip($filters));
        $this->range = $range;
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
                $this->createItems($number, $output);
        }
    }

    /**
     * Set up Faker.
     */
    private function prepare()
    {
        $this->faker = Factory::create();
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new Lorem($this->faker));
    }


    /**
     * Create a nuber of items.
     *
     * @param $number
     * @param OutputInterface $output
     */
    private function createItems($number, OutputInterface $output)
    {
        $this->prepare();

        for ($n = 0; $n < $number; $n++) {
            $item = $this->createItem();
            $this->manager->persist($item);
            $output->writeln(sprintf("Item '%s' created", $item->name));
        }

        $this->manager->commit();
    }

    /**
     * Create a random item
     * @return Item
     */
    private function createItem()
    {
        $item = new Item();

        $item->name = $this->faker->name();
        $item->text = $this->faker->paragraph(5);
        $item->location = $this->fakeLocation();
        $item->filter = $this->fakeFilter();;

        $this->setCategories($item);
        $this->setWeekdays($item);

        return $item;
    }


    /**
     * Set random weekdays on $item.
     * @param Item $item
     */
    private function setWeekdays(Item $item)
    {
        foreach ($this->subArray($this->weekdays, rand(1, 6)) as $day) {
            $item->days[] = new Day($day);
        }
    }

    /**
     * Set random categories on $item.
     * @param Item $item
     */
    private function setCategories(Item $item)
    {
        foreach ($this->subArray($this->categories, rand(1, 4)) as $cat) {
            $item->categories[] = new Category($cat);
        }
    }

    /**
     * Return an array with $n random items from $array.
     *
     * @param array $array
     * @param $n
     * @return array
     */
    private function subArray(array $array, $n)
    {
        return (array)array_rand(array_flip($array), $n);
    }

    /**
     * Random filter  name.
     *
     * @return string
     */
    private function fakeFilter()
    {
        return $this->subArray($this->filters, 1)[0];
    }

    /**
     * Random location within $this->range.
     *
     * @return string
     */
    private function fakeLocation()
    {
        $lat = $this->faker->latitude($this->range['lat']['min'], $this->range['lat']['max']);
        $lon = $this->faker->longitude($this->range['lon']['min'], $this->range['lon']['max']);
        return sprintf("%s,%s",$lat,$lon);
    }
}