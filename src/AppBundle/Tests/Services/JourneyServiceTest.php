<?php 

namespace AppBundle\Tests\Services;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class JourneyServiceTest extends BaseWebTestCase
{
    public function testImportGPX()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]); 
        
        $journeyService = $this->getContainer()->get('tb.journey');
        $journey = $this->getJourney('Test Journey 1');
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/GPX/example.gpx'),
            'example.gpx'
        );
        
        $result = $journeyService->importGPX($file, $journey);
        $this->assertInstanceOf('AppBundle\Response\APIResponse', $result);
        $this->assertEquals(2, count($result->getBody()['journeys'][0]->getRoutePoints()));
        $this->refreshEntity($journey);
        $this->assertEquals(2, count($journey->getRoutePoints()));
        
        $result = $journeyService->importGPX($file, $journey);
        $this->assertInstanceOf('AppBundle\Response\APIResponse', $result);
        $this->assertEquals(2, count($result->getBody()['journeys'][0]->getRoutePoints()));
        $this->assertEquals(2, count($journey->getRoutePoints()));
    }
    
    public function testDeleteJourneyRoutes()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]); 
        
        $journeyService = $this->getContainer()->get('tb.journey');
        $journey = $this->getJourney('Test Journey 1');
                
        $this->assertEquals(3, count($journey->getRoutePoints()));
        $result = $journeyService->deleteJourneyRoutePoints($journey->getOid());
        
        $this->refreshEntity($journey);
        $this->assertEquals(0, count($result->getBody()['journeys'][0]->getRoutePoints()));
        $this->assertEquals(0, count($journey->getRoutePoints()));
    }
}