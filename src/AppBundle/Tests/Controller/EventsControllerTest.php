<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EventsControllerTest extends BaseWebTestCase
{
        
    public function testGetByJourneyAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);
        
        $client = static::createClient();
        $journey = $this->getJourney('Test Journey 1');

        $client->request('GET', '/v2/journeys/' . $journey->getOid() . '/events');
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testGetByJourneyActionJourneyNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);
        
        $client = static::createClient();

        $client->request('GET', '/v2/journeys/99999999/events');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testGetByJourneyActionJourneyUnpublished()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);
        
        $client = static::createClient();
        $journey = $this->getJourney('Unpublished Journey');

        $client->request('GET', '/v2/journeys/' . $journey->getOid() . '/events');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
}
