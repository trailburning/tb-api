<?php 

namespace Tests\AppBundle\Services;

use Tests\AppBundle\BaseWebTestCase;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

class MapboxAPITest extends BaseWebTestCase
{
    public function testReverseGeocode()
    {
        $mapbox = $this->getMapboxAPIMock();
        $point = new Point(13.221316, 52.489695, 4326);

        $result = $mapbox->reverseGeocode($point);
        $this->assertEquals($result['place']->place_name, 'Berlin, Berlin, Germany');
        $this->assertEquals($result['region']->place_name, 'Berlin, Germany');
        $this->assertEquals($result['country']->place_name, 'Germany');
    }
    
    public function testCalculateDistance() 
    {
        $mapbox = $this->getMapboxAPIMock();
        $pointA = new Point(13.088304, 52.338079, 4326);
        $pointB = new Point(13.760909, 52.675323, 4326);
        $distance = $mapbox->calculateDistance($pointA, $pointB);
        
        $this->assertEquals($distance, 58978.657865492336);
        
        $pointA = new Point(5.866002975, 47.270461259, 4326);
        $pointB = new Point(15.041428463, 55.151372911, 4326);
        $distance = $mapbox->calculateDistance($pointA, $pointB);
        
        $this->assertEquals($distance, 1082715.6829980018);
    }
    
    public function testCalculateBoundingBoxRadius() 
    {
        $mapbox = $this->getMapboxAPIMock();
        $radius = $mapbox->calculateBoundingBoxRadius(13.088304, 52.338079, 13.760909, 52.675323);
                
        $this->assertEquals($radius, 29489);
    }
}