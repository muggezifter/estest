<?php

namespace AppBundle\Service;

use ONGR\ElasticsearchBundle\Service\Manager;
use Symfony\Component\HttpFoundation\Request;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchBundle\Result\Result;


class Matcher
{
    /**
     * Categories array.
     *
     * @var array
     */
    private $categories;

    /**
     * Folters array.
     *
     * @var
     */
    private $filters;

    /**
     * Weekdays array.
     *
     * @var array
     */
    private $weekdays;

    /**
     * ES Manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Construct a new matcher.
     *
     * @param Manager $manager
     * @param array $weekdays
     * @param array $categories
     * @param array $filters
     */
    public function __construct(
        Manager $manager,
        array $weekdays,
        array $categories,
        array $filters
    )
    {
        $this->manager = $manager;
        $this->weekdays = $weekdays;
        $this->categories = $categories;
        $this->filters = $filters;
    }

    /**
     * Return matches based on criteria in request.
     *
     * @param Request $request
     * @return array
     */
    public function match(Request $request)
    {
        $search = new Search();

        $search->addQuery(new MatchQuery("filter", $this->filters[$request->get("filter")]));
        $search->addQuery($this->makeBQ($request, $this->categories, 'categories', 2));
        $search->addQuery($this->makeBQ($request, $this->weekdays, 'days', 1));

        $queryArray = $search->toArray();

        $repo = $this->manager->getRepository('AppBundle:Item');
        $results = $repo->execute($search, Result::RESULTS_RAW_ITERATOR);
        $count = count($results);

        return compact("queryArray", "results", "count");
    }

    /**
     * Make BoolQuery for $type.
     *
     * @param Request $request
     * @param array $items
     * @param $type
     * @param $boost
     * @return BoolQuery
     */
    private function makeBQ(Request $request, array $items, $type, $boost)
    {
        $bq = new BoolQuery();
        $bq->addParameter("minimum_should_match", 1);
        $bq->addParameter("boost", $boost);

        foreach ($items as $code => $label) {
            $request->get($code) && $bq->add($this->makeMQ($type, $label, $code), BoolQuery::SHOULD);
        }
        return $bq;
    }

    /**
     * Make MatchQuery for $type.
     *
     * @param $type
     * @param $val
     * @param $name
     * @return MatchQuery
     */
    private function makeMQ($type, $val, $name)
    {
        $mq = new MatchQuery($type . ".name", $val);
        $mq->addParameter("_name", $name);
        return $mq;
    }

}
