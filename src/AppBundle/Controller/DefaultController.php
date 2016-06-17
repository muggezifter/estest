<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchBundle\Result\Result;
use AppBundle\Document\Item;

class DefaultController extends Controller
{
    /**
     * @var array
     */
    private $categories = [
       "neo"=>"Neo",
       "tri"=>"Trinity",
       "cyp"=>"Cypher",
       "mor"=>"Morpheus",
       "tan"=>"Tank",
    ];

    /**
     * @var array
     */
    private $weekdays = [
        "sun"=>"Sunday",
        "mon"=>"Monday",
        "tue"=>"Tuesday",
        "wed"=>"Wednesday",
        "thu"=>"Thursday",
        "fri"=>"Friday",
        "sat"=>"Saturday",
    ];

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/match", name="matchGet")
     * @Method({"GET"})
     */
    public function matchGetAction(Request $request){
        return $this->render('match/form.html.twig');
    }

    /**
     * @Route("/match", name="matchPost")
     * @Method({"POST"})
     */
    public function matchPostAction(Request $request)
    {
        $manager = $this->get('es.manager');
        $repo = $manager->getRepository('AppBundle:Item');

        $search = new Search();

        $filters = array("kel"=>"Kelvin","toa"=>"Toaster","lof"=>"Lo-Fi");
        $filtersMQ = new MatchQuery("filter", $filters[$request->get("filter")]);

        $catsBQ = new BoolQuery();
        $catsBQ->addParameter("minimum_should_match", 1);
        //$catsBQ->addParameter("boost", 1);

        foreach ($this->categories as $code=>$label) {
            $request->get($code) && $catsBQ->add($this->makeMQ('categories', $label, $code), BoolQuery::SHOULD);
        }

        $daysBQ = new BoolQuery();
        $daysBQ->addParameter("minimum_should_match", 1);

        foreach ($this->weekdays as $code=>$label) {
            $request->get($code) && $daysBQ->add($this->makeMQ('days', $label, $code), BoolQuery::SHOULD);
        }

        $search->addQuery($filtersMQ);
        $search->addQuery($catsBQ);
        $search->addQuery($daysBQ);

        $queryArray = $search->toArray();

        $results = $repo->execute($search,Result::RESULTS_RAW_ITERATOR);
        $count = count($results);
        return $this->render('match/result.html.twig', compact("queryArray","results","count"));
    }

    /**
     * Make MatchQuery for Days.
     *
     * @param $val
     * @param $name
     * @return MatchQuery
     */
    private function makeDaysMQ($val, $name){
        return $this->makeMQ('days',$val,$name);
    }

    /**
     * Make MatchQuery for Categories.
     *
     * @param $val
     * @param $name
     * @return MatchQuery
     */
    private function makeCatsMQ($val, $name){
        return $this->makeMQ('categories',$val,$name);
    }

    /**
     * Make MatchQuery for $type.
     *
     * @param $type
     * @param $val
     * @param $name
     * @return MatchQuery
     */
    private function makeMQ($type, $val, $name) {
        $mq = new MatchQuery($type.".name",$val);
        $mq->addParameter("_name",$name);
        return $mq;
    }
}
