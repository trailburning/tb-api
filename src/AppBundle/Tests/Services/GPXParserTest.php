<?php 

namespace AppBundle\Tests\Services;

use AppBundle\Tests\BaseWebTestCase;

class GPXParserTest extends BaseWebTestCase
{
    public function testParse()
    {
        $this->loadFixtures([]); 
        $parser = $this->getContainer()->get('tb.gpxParser');
        $routes = $parser->parse(file_get_contents(realpath(__DIR__ . '/../../DataFixtures/GPX/example.gpx')));
        $this->assertEquals(2, count($routes), '2 routes were found in .gpx file');
        $this->assertEquals(2, count($routes[0]), '2 route points were found for route 1');
        $this->assertEquals(2, count($routes[1]), '2 route points were found for route 2');
        $this->assertTrue(isset($routes[0][0]['lat']));
        $this->assertTrue(isset($routes[0][0]['long']));
        $this->assertTrue(isset($routes[0][0]['elevation']));
        $this->assertTrue(isset($routes[0][0]['datetime']));
    }
}