<?php

namespace AppBundle\Service;

use AppBundle\Document\Category;
use AppBundle\Document\Day;
use AppBundle\Document\Item;
use Faker;
use Faker\Factory;
use Faker\Provider\Lorem;
use Faker\Provider\nl_NL\Person;
use Symfony\Component\Console\Output\OutputInterface;

class Creator extends ServiceBase
{
    /**
     * @var Faker
     */
    private $faker = null;

    /**
     * Set up Faker.
     */
    private function prepare()
    {
        if (empty($this->faker)) {
            $this->faker = Factory::create();
            $this->faker->addProvider(new Person($this->faker));
            $this->faker->addProvider(new Lorem($this->faker));
        }
    }

    /**
     * Create a number of items.
     *
     * @param $number
     * @param OutputInterface $output
     */
    public function createItems($number, OutputInterface $output = null)
    {
        $this->prepare();

        while($number) {
            $item = $this->createItem();
            $this->manager->persist($item);
            $output && $output->writeln(sprintf("Item '%s' created", $item->name));
            $number--;
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
        foreach ($this->subArray(array_values($this->weekdays), rand(1, 6)) as $day) {
            $item->days[] = new Day($day);
        }
    }

    /**
     * Set random categories on $item.
     * @param Item $item
     */
    private function setCategories(Item $item)
    {
        foreach ($this->subArray(array_values($this->categories), rand(1, 4)) as $cat) {
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
        return $this->subArray(array_values($this->filters), 1)[0];
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
        return sprintf("%s,%s", $lat, $lon);
    }
}