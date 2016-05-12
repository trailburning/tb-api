<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\BaseWebTestCase;

class AssetsControllerTest extends BaseWebTestCase
{
    public function testGetByEventAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();
        $event = $this->getEvent('Test Event 1');

        $client->request('GET', '/v2/events/'.$event->getOid().'/assets');
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testGetByEventActionEventNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();

        $client->request('GET', '/v2/events/99999999/assets');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testGetAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);

        $client = $this->makeClient();
        $asset = $this->getAsset('Test Asset 1');

        $client->request('GET', '/v2/assets/'.$asset->getOid());
        $this->assertJsonResponse($client->getResponse(), 200);
    }

    public function testGetActionNotFound()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();

        $client->request('GET', '/v2/assets/99999999');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testPostAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
            'AppBundle\DataFixtures\ORM\AssetCategoryData',
        ]);

        $client = $this->makeClient();
        $event = $this->getEvent('Test Event 1');
        $category = $this->getAssetCategory('expedition');
        $data = [
            'name' => 'Test 123',
            'about' => 'about',
            'category' => $category->getId(),
            'position' => 1,
        ];

        $client->request('POST', '/v2/events/'.$event->getOid().'/assets', $data);
        $this->assertJsonResponse($client->getResponse(), 201);

        $asset = $this->getAsset('Test 123');
        $this->assertInstanceOf('AppBundle\Entity\Asset', $asset);
        $this->assertEquals('Test 123', $asset->getName());
        $this->assertEquals('about', $asset->getAbout());
        $this->assertEquals($event->getId(), $asset->getEvent()->getId());
        $this->assertEquals(1, $asset->getPosition());
        $this->assertTrue($client->getResponse()->headers->has('Location'), $client->getResponse()->headers);
    }

    public function testPostActionBadRequest()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\EventData',
        ]);

        $client = $this->makeClient();
        $event = $this->getEvent('Test Event 1');
        $data = [
            'name' => 'Test 123',
        ];

        $client->request('POST', '/v2/events/'.$event->getOid().'/assets', $data);
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function testPutAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);

        $client = $this->makeClient();
        $asset = $this->getAsset('Test Asset 1');
        $data = [
            'name' => 'Test 123',
            'about' => 'about',
        ];

        $client->request('PUT', '/v2/assets/' . $asset->getOid(), $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $this->refreshEntity($asset);
        $this->assertEquals('Test 123', $asset->getName());
        $this->assertEquals('about', $asset->getAbout());
    }

    public function testDeleteAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);

        $client = $this->makeClient();
        $asset = $this->getAsset('Test Asset 1');

        $client->request('DELETE', '/v2/assets/' . $asset->getOid());
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testDeleteActionNotFound()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();

        $client->request('DELETE', '/v2/assets/00000');
        $this->assertJsonResponse($client->getResponse(), 404);
    }
}
