<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\BaseWebTestCase;

class StatusControllerTest extends BaseWebTestCase
{
//    public function testGetAction()
//    {
//        $client = $this->makeClient();
//
//        $client->request('GET', '/v2');
//        $this->assertJsonResponse($client->getResponse(), 200);
//    }

    public function testGetSummaryAction()
    {
        $client = $this->makeClient();

        $client->request('GET', '/v2/summary');
        $this->assertJsonResponse($client->getResponse(), 200);
    }
}
