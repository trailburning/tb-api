<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\BaseWebTestCase;

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

    public function testGetAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();
        $event = $this->getEvent('Test Event 1');

        $client->request('GET', '/v2/events/'.$event->getOid());
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testGetActionNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();

        $client->request('GET', '/v2/events/99999999');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testPostAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Test Journey 1');
        $data = [
            'name' => 'Test 123',
            'about' => 'about',
            'position' => 1,
            'coords' => '(13.221316, 52.489695)',
            'custom' => [
                ['key' => 'key1', 'value' => 'value1'],
                ['key' => 'key2', 'value' => 'value2'],
            ],
        ];

        $client->request('POST', '/v2/journeys/'.$journey->getOid().'/events', $data);
        $this->assertJsonResponse($client->getResponse(), 201);

        $event = $this->getEvent('Test 123');
        $this->assertInstanceOf('AppBundle\Entity\Event', $event);
        $this->assertEquals('Test 123', $event->getName());
        $this->assertEquals('about', $event->getAbout());
        $this->assertEquals($journey->getId(), $event->getJourney()->getId());
        $this->assertEquals(1, $event->getPosition());
        $this->assertTrue($client->getResponse()->headers->has('Location'), $client->getResponse()->headers);
        $this->assertEquals(2, count($event->getCustom()));
    }

    public function testPostActionJSON()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Test Journey 1');
        $data = '{"name":"Test 123","about":"about","coords":"(13.221316, 52.489695)","custom":[{"key": "key1", "value": "value1"},{"key": "key2", "value": "value2"}]}';

        $client->request('POST',  '/v2/journeys/'.$journey->getOid().'/events', [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $this->assertJsonResponse($client->getResponse(), 201);

        $event = $this->getEvent('Test 123');
        $this->assertInstanceOf('AppBundle\Entity\Event', $event);
        $this->assertEquals(2, count($event->getCustom()));
    }

    public function testPostActionBadRequest()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Test Journey 1');
        $data = [
            'name' => 'Test 123',
        ];

        $client->request('POST', '/v2/journeys/'.$journey->getOid().'/events', $data);
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function testPutAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();
        $event = $this->getEvent('Test Event 1');
        $this->assertEquals(2, count($event->getCustom()));
        $data = [
            'name' => 'Test 123',
            'about' => 'about',
        ];

        $client->request('PUT', '/v2/events/' . $event->getOid(), $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $this->refreshEntity($event);
        $this->assertEquals('Test 123', $event->getName());
        $this->assertEquals('about', $event->getAbout());
        $this->assertEquals(2, count($event->getCustom()));
    }

    public function testPutActionCustomFieldsReplace()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();
        $event = $this->getEvent('Test Event 1');
        $this->assertEquals(2, count($event->getCustom()));
        $data = [
            'name' => 'Test 123',
            'about' => 'about',
            'custom' => [
                ['key' => 'key1', 'value' => 'value1'],
                ['key' => 'key2', 'value' => 'value2'],
            ],
        ];

        $client->request('PUT', '/v2/events/' . $event->getOid(), $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $this->refreshEntity($event);
        $this->assertEquals('Test 123', $event->getName());
        $this->assertEquals('about', $event->getAbout());
        $this->assertEquals(2, count($event->getCustom()));
    }

    public function testPutActionCustomFieldsDelete()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();
        $event = $this->getEvent('Test Event 1');
        $this->assertEquals(2, count($event->getCustom()));
        $data = [
            'name' => 'Test 123',
            'about' => 'about',
            'custom' => null,
        ];

        $client->request('PUT', '/v2/events/' . $event->getOid(), $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $this->refreshEntity($event);
        $this->assertEquals('Test 123', $event->getName());
        $this->assertEquals('about', $event->getAbout());
        $this->assertEquals(0, count($event->getCustom()));
    }

    public function testDeleteAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);

        $client = $this->makeClient();
        $event = $this->getEvent('Test Event 1');

        $client->request('DELETE', '/v2/events/' . $event->getOid());
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testDeleteActionNotFound()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();

        $client->request('DELETE', '/v2/events/0000');
        $this->assertJsonResponse($client->getResponse(), 404);
    }
}
