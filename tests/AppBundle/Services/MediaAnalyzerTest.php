<?php 

namespace Tests\AppBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\AppBundle\BaseWebTestCase;

class MediaAnalyzerTest extends BaseWebTestCase
{
    
    public function testGetMIMEType($value='') 
    {
        $mediaAnalyzer = $this->getContainer()->get('tb.media.analyzer');
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );
        
        $result = $mediaAnalyzer->getMIMEType($file);
        $this->assertEquals('image/jpeg', $result);
    }
}