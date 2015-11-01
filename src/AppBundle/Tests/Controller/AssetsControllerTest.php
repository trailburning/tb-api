<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class AssetsControllerTest extends BaseWebTestCase
{
    public function testGetByEventAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();
        $event = $this->getEvent('Test Event 1');

        $client->request('GET', '/v2/events/'.$event->getOid().'/assets');
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testGetByEventActionEventNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();

        $client->request('GET', '/v2/events/99999999/assets');
        $this->assertJsonResponse($client->getResponse(), 404);
    }
}
