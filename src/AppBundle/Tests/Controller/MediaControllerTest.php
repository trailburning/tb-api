<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaControllerTest extends BaseWebTestCase
{
        
    public function testPostAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);
        
        $client = static::createClient();
        $asset = $this->getAsset('Test Asset 1');
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $client->request('POST', '/v2/assets/' . $asset->getOid() . '/media', [], ['media' => $file]);
        $this->assertEquals(Response::HTTP_CREATED,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testPostActionAssetNotFound()
    {
        $this->loadFixtures([]);
        
        $client = static::createClient();
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $client->request('POST', '/v2/assets/000000/media', [], ['media' => $file]);
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testPostActionInvalidMIMEType()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);
        
        $client = static::createClient();
        $asset = $this->getAsset('Test Asset 1');
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/test.txt'),
            'test.txt'
        );
        
        $client->request('POST', '/v2/assets/' . $asset->getOid() . '/media', [], ['media' => $file]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
        
    // FIXME: multi media upload not working because of File validator
    // public function testPostActionMultipleMedia()
    // {
    //     $this->loadFixtures([
    //         'AppBundle\DataFixtures\ORM\AssetData',
    //     ]);
    //
    //     $client = static::createClient();
    //     $asset = $this->getAsset('Test Asset 1');
    //
    //     $file1 = new UploadedFile(
    //         realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
    //         'test.jpg'
    //     );
    //
    //     $file2 = new UploadedFile(
    //         realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
    //         'test.jpg'
    //     );
    //
    //     $this->assertEquals(2, count($asset->getMedias()));
    //
    //     $client->request('POST', '/v2/asset/' . $asset->getOid() . '/media', [], ['media' => [$file1, $file2]]);
    //     $this->assertEquals(Response::HTTP_CREATED,  $client->getResponse()->getStatusCode());
    //     $this->assertJsonResponse($client);
    //
    //     $this->refreshEntity();
    //     $this->assertEquals(4, count($asset->getMedias()));
    // }
}
