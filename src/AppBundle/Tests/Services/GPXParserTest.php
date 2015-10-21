<?php 

namespace AppBundle\Tests\Services;

use AppBundle\Tests\BaseWebTestCase;

class GPXParserTest extends BaseWebTestCase
{
    public function testParse()
    {
        $this->loadFixtures([]); 
        $parser = $this->getContainer()->get('tb.gpxParser');
        $segments = $parser->parse(file_get_contents(realpath(__DIR__ . '/../../DataFixtures/GPX/example.gpx')));
        $this->assertEquals(1, count($segments), '1 Segment was found in .gpx file');
        $this->assertEquals(2, count($segments[0]), '2 route points were found');
    }
}