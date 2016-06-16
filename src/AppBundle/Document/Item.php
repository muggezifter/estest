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
     *
     * @var string
     *
     * @ES\Property(type="geoPoint")
     */
    public $location;

}