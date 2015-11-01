<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class StatusControllerTest extends BaseWebTestCase
{
    public function testGetAction()
    {
        $client = $this->makeClient();

        $client->request('GET', '/v2/');
        $this->assertJsonResponse($client->getResponse(), 200);
    }
}
