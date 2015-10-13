<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SwaggerControllerTest extends BaseWebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();
        
        $client->request('GET', '/swagger.json');
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
}
