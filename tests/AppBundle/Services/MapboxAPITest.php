<?php 

namespace Tests\AppBundle\Services;

use Tests\AppBundle\BaseWebTestCase;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use AppBundle\Services\MapboxAPI;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class MapboxAPITest extends BaseWebTestCase
{
    public function testReverseGeocode()
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(__DIR__ . '/../../DataFixtures/Mapbox/reverse_geocode.json')),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $mapbox = new MapboxAPI($client, 'token');
        
        $point = new Point(13.221316, 52.489695, 4326);
        

        $result = $mapbox->reverseGeocode($point);
        
        $this->assertEquals($result, 'Berlin, Berlin, Germany'');
    }
}