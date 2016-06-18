<?php

namespace AppBundle\Service;

use ONGR\ElasticsearchBundle\Service\Manager;

class ServiceBase {
    /**
     * @var Manager
     */
    protected $manager;
    /**
     * @var array
     */
    protected $weekdays;
    /**
     * @var array
     */
    protected $categories;
    /**
     * @var array
     */
    protected $filters;
    /**
     * @var array
     */
    protected $range;
    /**
     * Constructor.
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
        $this->manager = $manager;
        $this->weekdays = $weekdays;
        $this->categories = $categories;
        $this->filters = $filters;
        $this->range = $range;
    }
}