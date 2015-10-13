<?php

namespace AppBundle\Tests\Services;

use AppBundle\Tests\BaseWebTestCase;
use AppBundle\Services\SwaggerBridge;

class SwaggerBridgeTest extends BaseWebTestCase
{
    public function testGenerateJson()
    {
        $swaggerBridge = $this->getContainer()->get('tb.services.swagger_bridge');
        @unlink($swaggerBridge->getCachePath());
        
        $result = $swaggerBridge->generateJson();
        $this->assertJson($result);
    }
    
    public function testGenerateJsonReadsFromCache()
    {
        $swaggerBridge = new SwaggerBridge($this->getContainer()->get('kernel')->getRootDir(), 'dev', false);
        file_put_contents($swaggerBridge->getCachePath(), 'cache-test');
        
        $result = $swaggerBridge->generateJson();
        $this->assertEquals('cache-test', $result);
        @unlink($swaggerBridge->getCachePath());
    }
}
