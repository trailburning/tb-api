<?php

namespace AppBundle\Tests\Controller;

use Tests\AppBundle\BaseWebTestCase;

class RaceControllerTest extends BaseWebTestCase
{

    public function testGetAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceData',
        ]);

        $client = $this->makeClient();
        $raceRepository = $this->getContainer()->get('app.repository.race');
        $race = $raceRepository->findAll()[0];

        $client->request('GET', '/v2/races/'.$race->getOid());
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testGetActionNotFound()
    {
        $client = $this->makeClient();

        $client->request('GET', '/v2/races/0');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testGetByEventRaceAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceData',
        ]);

        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];

        $client->request('GET', '/v2/raceevents/' . $raceEvent->getOid() . '/races');
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testPostAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];
        $data = [
            'name' => 'name',
            'date' => '2016-05-17',
            'type' => 'road',
            'distance' => 'marathon',
        ];
        
        $client->request('POST', '/v2/raceevents/' . $raceEvent->getOid() . '/races', $data);
        $this->assertJsonResponse($client->getResponse(), 201);
    }

    public function testPostActionBadRequest()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);
        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];
        $data = [];

        $client->request('POST', '/v2/raceevents/' . $raceEvent->getOid() . '/races', $data);
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function testPutAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceData',
        ]);

        $client = $this->makeClient();
        $raceRepository = $this->getContainer()->get('app.repository.race');
        $race = $raceRepository->findAll()[0];
        $data = [
            'name' => 'name',
            'date' => '2016-05-17',
            'type' => 'road',
            'distance' => 'marathon',
        ];

        $client->request('PUT', '/v2/races/'.$race->getOid(), $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testPutActionNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceData',
        ]);

        $client = $this->makeClient();

        $client->request('PUT', '/v2/races/0', []);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDeleteAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceData',
        ]);

        $client = $this->makeClient();
        $raceRepository = $this->getContainer()->get('app.repository.race');
        $race = $raceRepository->findAll()[0];

        $client->request('DELETE', '/v2/races/'.$race->getOid());
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testDeleteActionNotFound()
    {
        $client = $this->makeClient();

        $client->request('DELETE', '/v2/races/0');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

}
