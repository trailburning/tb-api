<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SwaggerController extends Controller
{
    public function indexAction()
    {
        ini_set('memory_limit', '512M');
        $swaggerBridge = $this->get('tb.swaggerBridge');
        $jsonDoc = $swaggerBridge->generateJson();
        $response = new Response($jsonDoc);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
