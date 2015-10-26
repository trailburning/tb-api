<?php 

namespace AppBundle\Tests\Services;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaServiceTest extends BaseWebTestCase
{
    public function testCreateMedia()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\AssetData',
        ]);
        
        $mediaService = $this->getContainer()->get('tb.media');
        $filesystem = $this->getContainer()->get('debug_filesystem');
        $mediaService->setFilesystem($filesystem);
        $asset = $this->getAsset('Test Asset 1');
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $result = $mediaService->createMedia([$file], $asset);
        $this->refreshEntity($asset);
        
        $this->assertEquals(3, count($asset->getMedias()));
    }
    
    public function testUploadFile()
    {
        $mediaService = $this->getContainer()->get('tb.media');
        $filesystem = $this->getContainer()->get('debug_filesystem');
        $mediaService->setFilesystem($filesystem);
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $filepath = $mediaService->uploadFile($file);
        $this->assertTrue($filesystem->has($filepath), 'The file exists on the provided filesystem');
        $this->assertRegExp('/\/images\/[\d\w]+\.jpg/', $filepath, 'The files path was retuned by the uploadFile() method');
    }
    
    public function testGetMIMEType($value='') 
    {
        $mediaService = $this->getContainer()->get('tb.media');
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $result = $this->callProtectedMethod($mediaService, 'getMIMEType', [$file]);
        $this->assertEquals('image/jpeg', $result);
    }
    
    public function testGenerateRelativeFilepath($value='')
    {
        $mediaService = $this->getContainer()->get('tb.media');
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $result = $this->callProtectedMethod($mediaService, 'generateRelativeFilepath', [$file]);
        $this->assertRegExp('/\/images\/[\d\w]+\.jpg/', $result);
    }
}