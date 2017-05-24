<?php

namespace tests\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\BaseWebTestCase;

class DefaultControllerTest extends BaseWebTestCase
{
    public function testIndexAction()
    {
        $client = $this->makeClient();

        $client->request('GET', '/');
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
    }

    public function testStatusAction()
    {
        $client = $this->makeClient();

        $client->request('GET', '/v2');
        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY,  $client->getResponse()->getStatusCode());
    }
}
