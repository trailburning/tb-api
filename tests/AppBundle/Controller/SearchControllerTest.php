<?php

namespace AppBundle\Tests\Controller;

use Tests\AppBundle\BaseWebTestCase;

class SearchControllerTest extends BaseWebTestCase
{
    public function testSearchAction()
    {
        $client = $this->makeClient();
        $client->request('GET', '/v2/search');
        $this->assertJsonResponse($client->getResponse(), 200);
    }
}
