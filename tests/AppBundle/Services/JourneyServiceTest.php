<?php 

namespace Tests\AppBundle\Services;

use AppBundle\Entity\Journey;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\AppBundle\BaseWebTestCase;

class JourneyServiceTest extends BaseWebTestCase
{
    public function testImportGPX()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]); 
        
        $journeyService = $this->getContainer()->get('app.journey');
        $journey = $this->getJourney('Test Journey 1');
        
        $file = new UploadedFile(
            realpath(__DIR__ . '/../../DataFixtures/GPX/example.gpx'),
            'example.gpx'
        );
        
        $result = $journeyService->importGPX($file, $journey);
        $this->assertInstanceOf('AppBundle\Model\APIResponse', $result);
        $this->refreshEntity($journey);
        $this->assertEquals(2, count($journey->getRoutePoints()));
        $this->assertNotNull($journey->getRoutePoints()[0]->getElevation());
        
        $result = $journeyService->importGPX($file, $journey);
        $this->assertInstanceOf('AppBundle\Model\APIResponse', $result);
        $this->assertEquals(2, count($journey->getRoutePoints()));
    }
    
    public function testDeleteJourneyRoutes()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\JourneyData',
        ]); 
        
        $journeyService = $this->getContainer()->get('app.journey');
        $journey = $this->getJourney('Test Journey 1');
                
        $this->assertEquals(3, count($journey->getRoutePoints()));
        $result = $journeyService->deleteJourneyRoutePoints($journey->getOid());
        
        $this->refreshEntity($journey);
        $this->assertEquals(0, count($result->getBody()['journeys'][0]->getRoutePoints()));
        $this->assertEquals(0, count($journey->getRoutePoints()));
    }
}