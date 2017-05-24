<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\BaseWebTestCase;

class SummaryControllerTest extends BaseWebTestCase
{
    public function testGetAction()
    {
        $client = $this->makeClient();

        $client->request('GET', '/v2/summary');
        $this->assertJsonResponse($client->getResponse(), 200);
    }
}
