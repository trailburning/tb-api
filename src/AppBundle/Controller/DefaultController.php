<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return new Response('Trailburning Journey API');
    }
    
    /**
     * @Route("/v2")
     */
    public function v2Action()
    {
        return $this->redirect($this->generateUrl('get'), 301);
    }
}
