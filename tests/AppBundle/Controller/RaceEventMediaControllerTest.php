<?php

namespace tests\AppBundle\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\AppBundle\BaseWebTestCase;

class RaceEventMediaControllerTest extends BaseWebTestCase
{
    public function testPostActionGreen()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');
        $this->createRaceEventInSearch($raceEvent);

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );

        $client->request('POST', '/v2/raceevents/'.$raceEvent->getOid().'/media', [], ['media' => $file]);
        $this->assertJsonResponse($client->getResponse(), 201);
    }

    public function testPostActionRaceEventNotFound()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );

        $client->request('POST', '/v2/raceevents/000000/media', [], ['media' => $file]);
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testPostActionInvalidMIMEType()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/test.txt'),
            'test.txt'
        );

        $client->request('POST', '/v2/raceevents/'.$raceEvent->getOid().'/media', [], ['media' => $file]);
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function testPostActionMP4()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');
        $this->createRaceEventInSearch($raceEvent);

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/Media/test.m4v'),
            'test.m4v'
        );

        $client->request('POST', '/v2/raceevents/'.$raceEvent->getOid().'/media', [], ['media' => $file]);
        $this->assertJsonResponse($client->getResponse(), 201);
    }

    public function testPostActionMPEG()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');
        $this->createRaceEventInSearch($raceEvent);

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/Media/test.mp3'),
            'test.mp3'
        );

        $client->request('POST', '/v2/raceevents/'.$raceEvent->getOid().'/media', [], ['media' => $file]);
        $this->assertJsonResponse($client->getResponse(), 201);
    }

    public function testDeleteAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');
        $media = $raceEvent->getMedias()[0];

        $client->request('DELETE', '/v2/raceevents/'.$raceEvent->getOid().'/media/'.$media->getOid());
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testDeleteActionRaceEventNotFound()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();

        $client->request('DELETE', '/v2/raceevents/00000/media/00000');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testDeleteActionMediaNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');

        $client->request('DELETE', '/v2/raceevents/'.$raceEvent->getOid().'/media/00000');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testPutActionGreen()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');
        $this->createRaceEventInSearch($raceEvent);
        $media = $raceEvent->getMedias()[0];

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );

        $client->request('POST', '/v2/raceevents/'.$raceEvent->getOid().'/media/'.$media->getOid(), [], ['media' => $file]);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testPutActionRaceEventNotFound()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );

        $client->request('POST', '/v2/raceevents/0000/media/0000', [], ['media' => $file]);
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testPutActionMediaNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );

        $client->request('POST', '/v2/raceevents/'.$raceEvent->getOid().'/media/0000', [], ['media' => $file]);
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testPutActionMP4()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');
        $this->createRaceEventInSearch($raceEvent);
        $media = $raceEvent->getMedias()[0];

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/Media/test.m4v'),
            'test.m4v'
        );

        $client->request('POST', '/v2/raceevents/'.$raceEvent->getOid().'/media/'.$media->getOid(), [], ['media' => $file]);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testPutActionMPEG()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEvent = $this->getRaceEvent('Engadin Ultraks');
        $this->createRaceEventInSearch($raceEvent);
        $media = $raceEvent->getMedias()[0];

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/Media/test.mp3'),
            'test.mp3'
        );

        $client->request('POST', '/v2/raceevents/'.$raceEvent->getOid().'/media/'.$media->getOid(), [], ['media' => $file]);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }
}
