<?php

namespace Tests\AppBundle\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\AppBundle\BaseWebTestCase;

class JourneysControllerTest extends BaseWebTestCase
{
    public function testGetAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Test Journey 1');

        $client->request('GET', '/v2/journeys/'.$journey->getOid());
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testGetActionNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();

        $client->request('GET', '/v2/journeys/99999999');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testGetActionUnpublishedNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Unpublished Journey');

        $client->request('GET', '/v2/journeys/'.$journey->getOid());
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testGetByUserAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();
        $user = $this->getUser('mattallbeury');

        $client->request('GET', '/v2/journeys/user/'.$user->getId());
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testGetByUserActionUserNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();

        $client->request('GET', '/v2/journeys/user/99999999');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testImportGPXAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Test Journey 1');

        $file = new UploadedFile(
            realpath(__DIR__.'/../../DataFixtures/GPX/example.gpx'),
            'example.gpx'
        );

        $client->request('POST', '/v2/journeys/'.$journey->getOid().'/import/gpx', [], ['file' => $file]);
        $this->assertJsonResponse($client->getResponse(), 201);
    }

    public function testPostAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
        ]);

        $client = $this->makeClient();
        $user = $this->getUser('mattallbeury');
        $data = [
            'name' => 'Test 123',
            'about' => 'about',
            'user' => $user->getId(),
            'position' => 1,
            'publish' => 'true',
        ];

        $client->request('POST', '/v2/journeys', $data);
        $this->assertJsonResponse($client->getResponse(), 201);

        $journey = $this->getJourney('Test 123');
        $this->assertInstanceOf('AppBundle\Entity\Journey', $journey);
        $this->assertEquals('Test 123', $journey->getName());
        $this->assertEquals('about', $journey->getAbout());
        $this->assertEquals($user->getId(), $journey->getUser()->getId());
        $this->assertEquals(1, $journey->getPosition());
        $this->assertEquals(true, $journey->isPublish());
        $this->assertTrue($client->getResponse()->headers->has('Location'), $client->getResponse()->headers);
    }
    
    public function testPostActionJSON()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
        ]);

        $client = $this->makeClient();
        $user = $this->getUser('mattallbeury');
        $data = '{"name":"Test 123","about":"about","user":'.$user->getId().'}';
        
        $client->request('POST',  '/v2/journeys', [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $this->assertJsonResponse($client->getResponse(), 201);

        $journey = $this->getJourney('Test 123');
        $this->assertInstanceOf('AppBundle\Entity\Journey', $journey);
    }
    
    public function testPostActionBadRequest()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
        ]);

        $client = $this->makeClient();
        $user = $this->getUser('mattallbeury');
        $data = [
            'name' => 'Test 123',
        ];

        $client->request('POST', '/v2/journeys', $data);
        $this->assertJsonResponse($client->getResponse(), 400);
    }
    
    public function testPutAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Test Journey 1');
        $data = [
            'name' => 'Test 123',
            'about' => 'about',
        ];

        $client->request('PUT', '/v2/journeys/' . $journey->getOid(), $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $this->refreshEntity($journey);
        $this->assertEquals('Test 123', $journey->getName());
        $this->assertEquals('about', $journey->getAbout());
    }
    
    public function testDeleteAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
            'AppBundle\DataFixtures\ORM\EventData',
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);

        $client = $this->makeClient();
        $journey = $this->getJourney('Test Journey 1');

        $client->request('DELETE', '/v2/journeys/' . $journey->getOid());
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }
    
    public function testDeleteActionNotFound()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();

        $client->request('DELETE', '/v2/journeys/0000');
        $this->assertJsonResponse($client->getResponse(), 404);
    }
}
