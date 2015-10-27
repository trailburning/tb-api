<?php 

namespace AppBundle\Tests\Services\MetadataReader;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\File\File;

class JPEGMetadataReaderTest extends BaseWebTestCase
{
    public function testRead() 
    {
        $reader = $this->getContainer()->get('tb.media.metadata.jpeg');
        $file = new File(realpath(__DIR__ . '/../../../DataFixtures/Media/test.jpg'));
        
        $result = $reader->read($file);
        $this->assertEquals('58447', $result['filesize']);
        $this->assertEquals(640, $result['width']);
        $this->assertEquals(480, $result['height']);
    }
}