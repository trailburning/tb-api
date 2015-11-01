<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class EventsControllerTest extends BaseWebTestCase
{
    public function testGetByJourneyAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Test Journey 1');

        $client->request('GET', '/v2/journeys/'.$journey->getOid().'/events');
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testGetByJourneyActionJourneyNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();

        $client->request('GET', '/v2/journeys/99999999/events');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testGetByJourneyActionJourneyUnpublished()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Unpublished Journey');

        $client->request('GET', '/v2/journeys/'.$journey->getOid().'/events');
        $this->assertJsonResponse($client->getResponse(), 404);
    }
}
