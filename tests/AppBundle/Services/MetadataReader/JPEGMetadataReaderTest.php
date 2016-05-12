<?php 

namespace Tests\AppBundle\Services\MetadataReader;

use Symfony\Component\HttpFoundation\File\File;
use Tests\AppBundle\BaseWebTestCase;

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
    
    public function testReadGPS() 
    {
        $reader = $this->getContainer()->get('tb.media.metadata.jpeg');
        $file = new File(realpath(__DIR__ . '/../../../DataFixtures/Media/test_gps.jpg'));
        
        $result = $reader->read($file);
        $this->assertEquals(-3.50391944444, $result['longitude']);
        $this->assertEquals(55.5488555556, $result['latitude']);
    }
    
    public function testReadDimensions() 
    {
        $reader = $this->getContainer()->get('tb.media.metadata.jpeg');
        $file = new File(realpath(__DIR__ . '/../../../DataFixtures/Media/test_dimensions.jpg'));
        
        $result = $reader->read($file);
        $this->assertEquals(3456, $result['width']);
        $this->assertEquals(4608, $result['height']);
    }
}