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

        $bool = new BoolQuery();
        $bool->addParameter("minimum_should_match", 1);
        $bool->addParameter("boost", 1);

        $request->get("neo") && $bool->add($this->makeMQ("Neo", "neo"), BoolQuery::SHOULD);
        $request->get("tri") && $bool->add($this->makeMQ("Trinity", "tri"), BoolQuery::SHOULD);
        $request->get("cyp") && $bool->add($this->makeMQ("Cypher", "cyp"), BoolQuery::SHOULD);
        $request->get("mor") && $bool->add($this->makeMQ("Morpheus", "mor"), BoolQuery::SHOULD);
        $request->get("tan") && $bool->add($this->makeMQ("Tank", "tan"), BoolQuery::SHOULD);

        $search->addQuery($bool);

        $queryArray = $search->toArray();

        $results = $repo->execute($search,Result::RESULTS_RAW_ITERATOR);
        $count = count($results);
        return $this->render('match/result.html.twig', compact("queryArray","results","count"));
    }

    function makeMQ($val, $name) {
        $mq = new MatchQuery("categories.name",$val);
        $mq->addParameter("_name",$name);
        return $mq;
    }
}
