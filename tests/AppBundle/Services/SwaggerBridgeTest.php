<?php

namespace Tests\AppBundle\Services;

use AppBundle\Services\SwaggerBridge;
use Tests\AppBundle\BaseWebTestCase;

class SwaggerBridgeTest extends BaseWebTestCase
{
    public function testGenerateJson()
    {
        $swaggerBridge = $this->getContainer()->get('app.swaggerBridge');
        @unlink($swaggerBridge->getCachePath());
        
        $result = $swaggerBridge->generateJson();
        $this->assertJson($result);
    }
    
    public function testGenerateJsonReadsFromCache()
    {
        $swaggerBridge = new SwaggerBridge($this->getContainer()->get('kernel')->getRootDir(), 'test', false);
        file_put_contents($swaggerBridge->getCachePath(), 'cache-test');
        
        $result = $swaggerBridge->generateJson();
        $this->assertEquals('cache-test', $result);
        @unlink($swaggerBridge->getCachePath());
    }
}
