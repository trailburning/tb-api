<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends BaseWebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
    }
    
    public function testStatusAction()
    {
        $client = static::createClient();

        $client->request('GET', '/v2');
        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY,  $client->getResponse()->getStatusCode());
    }
}
