<?php 

namespace Tests\AppBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\AppBundle\BaseWebTestCase;

class MediaServiceTest extends BaseWebTestCase
{
    public function testUploadFile()
    {
        $mediaService = $this->getContainer()->get('app.media.assets');
        $filesystem = $this->getContainer()->get('debug_filesystem');
        $mediaService->setFilesystem($filesystem);
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $filepath = $mediaService->uploadFile($file);
        $this->assertTrue($filesystem->has($filepath), 'The file exists on the provided filesystem');
        $this->assertRegExp('/25zero\/[\d\w]+\.jpg/', $filepath, 'The files path was retuned by the uploadFile() method');
    }
        
    public function testGenerateRelativeFilepath()
    {
        $mediaService = $this->getContainer()->get('app.media.assets');
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $result = $this->callProtectedMethod($mediaService, 'generateRelativeFilepath', [$file]);
        $this->assertRegExp('/25zero\/[\d\w]+\.jpg/', $result);
    }
}