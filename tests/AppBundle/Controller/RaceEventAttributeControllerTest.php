<?php

namespace AppBundle\Tests\Controller;

use Tests\AppBundle\BaseWebTestCase;

class RaceEventAttributeControllerTest extends BaseWebTestCase
{

    public function testGetListAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventAttributeData',
        ]);

        $client = $this->makeClient();
        
        $client->request('GET', '/v2/raceevents/attributes');
        $this->assertJsonResponse($client->getResponse(), 200);        
    }
    
    public function testGetRaceEventListAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];
        
        $this->assertEquals(count($raceEvent->getAttributes()), 2);
        
        $client->request('GET', '/v2/raceevents/'. $raceEvent->getOid() . '/attributes');
        $this->assertJsonResponse($client->getResponse(), 200);        
    }

    public function testPutAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];
        $raceEvent->clearAttributes();
        $raceEventRepository->add($raceEvent);
        $raceEventRepository->store();
        $this->assertEquals(count($raceEvent->getAttributes()), 0);
        
        $raceEventAttributeRepository = $this->getContainer()->get('app.repository.race_event_attribute');
        $raceEventAttribute = $raceEventAttributeRepository->findAll()[0];

        $client->request('PUT', '/v2/raceevents/' . $raceEvent->getOid() . '/attributes/' . $raceEventAttribute->getId(), []);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        
        $raceEventRepository->refresh($raceEvent);
        $this->assertEquals(count($raceEvent->getAttributes()), 1);
    }
    
    public function testPutActionRaceEventNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();

        $client->request('PUT', '/v2/raceevents/0/attributes/0', []);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testPutActionRaceEventAttributeNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];

        $client->request('PUT', '/v2/raceevents/' . $raceEvent->getOid() . '/attributes/0', []);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testDeleteAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];
        $this->assertEquals(count($raceEvent->getAttributes()), 2);
        
        $raceEventAttribute = $raceEvent->getAttributes()[0];

        $client->request('DELETE', '/v2/raceevents/' . $raceEvent->getOid() . '/attributes/' . $raceEventAttribute->getId());
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        
        $raceEventRepository->refresh($raceEvent);
        $this->assertEquals(count($raceEvent->getAttributes()), 1);
    }
    
    public function testDeleteActionRaceEventNotFound()
    {
        $client = $this->makeClient();

        $client->request('DELETE', '/v2/raceevents/0/attributes/0');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testDeleteActionRaceEventAttributeNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);
        
        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];

        $client->request('DELETE', '/v2/raceevents/' . $raceEvent->getOid() . '/attributes/0');
        $this->assertJsonResponse($client->getResponse(), 404);
    }
    
}
