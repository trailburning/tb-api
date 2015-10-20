<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class JourneysControllerTest extends BaseWebTestCase
{
    public function testGetAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);
        
        $client = static::createClient();
        $journey = $this->getJourney('Test Journey 1');

        $client->request('GET', '/v2/journeys/' . $journey->getOid());
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testGetActionNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);
        
        $client = static::createClient();

        $client->request('GET', '/v2/journeys/99999999');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testGetActionUnpublishedNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);
        
        $client = static::createClient();
        $journey = $this->getJourney('Unpublished Journey');

        $client->request('GET', '/v2/journeys/' . $journey->getOid());
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testGetByUserAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);
        
        $client = static::createClient();
        $user = $this->getUser('mattallbeury');

        $client->request('GET', '/v2/journeys/user/' . $user->getId());
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testGetByUserActionUserNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]);
        
        $client = static::createClient();

        $client->request('GET', '/v2/journeys/user/99999999');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
}
