<?php

namespace AppBundle\Tests\Controller;

use Tests\AppBundle\BaseWebTestCase;

class RaceEventControllerTest extends BaseWebTestCase
{
 
    public function testGetAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];
        $client->request('GET', '/v2/raceevents/'. $raceEvent->getOid());
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testGetActionNotFound()
    {
        $client = $this->makeClient();

        $client->request('GET', '/v2/raceevents/0');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testGetListAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();

        $client->request('GET', '/v2/raceevents');
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testPostAction()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();
        $data = [
            'name' => 'name',
            'about' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'website' => 'website',
            'coords' => '(13.221316, 52.489695)',
        ];

        $client->request('POST', '/v2/raceevents', $data);
        $this->assertJsonResponse($client->getResponse(), 201);
    }

    public function testPostActionBadRequest()
    {
        $client = $this->makeClient();
        $data = [];

        $client->request('POST', '/v2/raceevents', $data);
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function testPutAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        $raceEvent = $raceEventRepository->findAll()[0];
        $data = [
            'name' => 'name',
            'about' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'website' => 'website',
            'coords' => '(13.221316, 52.489695)',
        ];

        $client->request('PUT', '/v2/raceevents/'.$raceEvent->getOid(), $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testPutActionNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();

        $client->request('PUT', '/v2/raceevents/0', []);
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

        $client->request('DELETE', '/v2/raceevents/'.$raceEvent->getOid());
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testDeleteActionNotFound()
    {
        $client = $this->makeClient();

        $client->request('DELETE', '/v2/raceevents/0');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

}
