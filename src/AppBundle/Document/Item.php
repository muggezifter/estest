<?php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Collection\Collection;

/**
 * @ES\Document()
 */
class Item
{
    /**
     * Initialize collection.
     */
    public function __construct()
    {
        $this->categories = new Collection();
        $this->days = new Collection();
    }
    /**
     * @var string
     *
     * @ES\Id()
     */
    public $id;
    /**
     * @var string
     *
     * @ES\Property(name="name", type="string")
     */
    public $name;
    /**
     * @var string
     *
     * @ES\Property(name="filter", type="string")
     */
    public $filter;
    /**
     * @var string
     *
     * @ES\Property(name="text", type="string")
     */
    public $text;
    /**
     * @var Category[]|Collection
     *
     * @ES\Embedded(class="AppBundle:Category", multiple=true)
     */
    public $categories;
    /**
     * @var Day[]|Collection
     *
     * @ES\Embedded(class="AppBundle:Day", multiple=true)
     */
    public $days;
    /**
     *
     * @var string
     *
     * @ES\Property(type="geoPoint")
     */
    public $location;

}