<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\Matcher;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/match", name="matchGet")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function matchGetAction(Request $request)
    {
        return $this->render('match/form.html.twig');
    }

    /**
     * @Route("/match", name="matchPost")
     * @Method({"POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function matchPostAction(Request $request)
    {
//        $matcher = new Matcher(
//            $this->get('es.manager'),
//            $this->getParameter('estest.items.weekdays')
//        );

        $matcher = $this->get('app.estest.matcher');
        return $this->render('match/result.html.twig', $matcher->match($request));
    }
}
