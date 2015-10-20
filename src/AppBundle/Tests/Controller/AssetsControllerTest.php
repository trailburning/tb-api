<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AssetsControllerTest extends BaseWebTestCase
{
        
    public function testGetByEventAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);
        
        $client = static::createClient();
        $event = $this->getEvent('Test Event 1');

        $client->request('GET', '/v2/events/' . $event->getOid() . '/assets');
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testGetByEventActionEventNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);
        
        $client = static::createClient();

        $client->request('GET', '/v2/events/99999999/assets');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
}
