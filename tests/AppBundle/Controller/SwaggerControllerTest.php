<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\BaseWebTestCase;

class SwaggerControllerTest extends BaseWebTestCase
{
    public function testIndexAction()
    {
        $client = $this->makeClient();

        $client->request('GET', '/v2/swagger.json');
        $this->assertJsonResponse($client->getResponse(), 200);
    }
}
