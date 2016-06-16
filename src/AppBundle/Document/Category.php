<?php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * @ES\Object
 */
class Category
{
    /**
     * Initialize collection.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    /**
     * @ES\Property(type="string")
     */
    public $key;

    /**
     * @ES\Property(type="string")
     */
    public $name;
}