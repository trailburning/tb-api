<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class StatusControllerTest extends BaseWebTestCase
{
    public function testGetAction()
    {
        $client = static::createClient();

        $client->request('GET', '/v2/');
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
}
