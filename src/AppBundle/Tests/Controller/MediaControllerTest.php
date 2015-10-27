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
        
        $client = $this->makeClient();
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
        
        $client = $this->makeClient();
        
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
        
        $client = $this->makeClient();
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
    //     $client = $this->makeClient();
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
    
    public function testDeleteAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);
        
        $client = $this->makeClient();
        $asset = $this->getAsset('Test Asset 1');
        $media = $asset->getMedias()[0];
        
        $client->request('DELETE', '/v2/assets/' . $asset->getOid() . '/media/' . $media->getOid());
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testDeleteActionAssetNotFound()
    {
        $this->loadFixtures([]);
        
        $client = $this->makeClient();
        
        $client->request('DELETE', '/v2/assets/00000/media/00000');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testDeleteActionMediaNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);
        
        $client = $this->makeClient();
        $asset = $this->getAsset('Test Asset 1');
        
        $client->request('DELETE', '/v2/assets/' . $asset->getOid() . '/media/00000');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testPutAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);
        
        $client = $this->makeClient();
        $asset = $this->getAsset('Test Asset 1');
        $media = $asset->getMedias()[0];
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $client->request('POST', '/v2/assets/' . $asset->getOid() . '/media/' . $media->getOid(), [], ['media' => $file]);
        $this->assertEquals(Response::HTTP_OK,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testPutActionAssetNotFound()
    {
        $this->loadFixtures([]);
        
        $client = $this->makeClient();
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $client->request('POST', '/v2/assets/0000/media/0000', [], ['media' => $file]);
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
    
    public function testPutActionMediaNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);
        
        $client = $this->makeClient();
        $asset = $this->getAsset('Test Asset 1');
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $client->request('POST', '/v2/assets/' . $asset->getOid() . '/media/0000', [], ['media' => $file]);
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $client->getResponse()->getStatusCode());
        $this->assertJsonResponse($client);
    }
}
